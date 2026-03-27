<?php

namespace App\Http\Controllers;

use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use App\Models\Tenant;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosCashierController extends Controller
{
    private function getTenant(Request $request): Tenant
    {
        return $request->_pos_tenant;
    }

    // ─── Halaman Utama ────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $tenant = $this->getTenant($request);

        $categories = Category::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'color']);

        // Tenant lain milik user ini (untuk fitur switch toko)
        $userTenants = Auth::guard('pos')->user()
            ->tenants()
            ->where('tenants.is_active', true)
            ->where('tenants.id', '!=', $tenant->id)
            ->get(['tenants.id', 'tenants.name']);

        return view('pos.index', compact('categories', 'tenant', 'userTenants'));
    }

    // ─── API: Produk ──────────────────────────────────────────────────────

    public function products(Request $request): JsonResponse
    {
        $tenant = $this->getTenant($request);

        $products = Product::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'price', 'stock', 'track_stock', 'image', 'category_id']);

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->price,
            'stock' => $p->track_stock ? $p->stock : null,
            'in_stock' => $p->isInStock(),
            'image' => $p->image ? asset('storage/' . $p->image) : null,
            'category_id' => $p->category_id,
        ]));
    }

    // ─── API: Buat Sale ───────────────────────────────────────────────────

    public function createSale(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['sometimes', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = $this->getTenant($request);
        $user = Auth::guard('pos')->user();

        // Pastikan semua product_id milik tenant yang aktif
        $productIds = collect($request->items)->pluck('product_id');
        $validProducts = Product::where('tenant_id', $tenant->id)
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($validProducts->count() !== $productIds->unique()->count()) {
            return response()->json(['message' => 'Terdapat produk tidak valid.'], 422);
        }

        $sale = DB::transaction(function () use ($request, $tenant, $user, $validProducts) {
            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'status' => Sale::STATUS_PENDING,
                'notes' => $request->notes,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'paid' => 0,
                'change' => 0,
            ]);

            foreach ($request->items as $item) {
                $product = $validProducts[$item['product_id']];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'cost_price' => $product->cost_price ?? 0,
                    'qty' => $item['qty'],
                    'discount' => $item['discount'] ?? 0,
                ]);
            }

            return $sale->refresh();
        });

        return response()->json([
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'subtotal' => (float) $sale->subtotal,
            'total' => (float) $sale->total,
        ]);
    }

    // ─── API: Proses Bayar ────────────────────────────────────────────────

    public function processSale(Request $request, Sale $sale): JsonResponse
    {
        // Pastikan sale milik tenant aktif
        if ($sale->tenant_id !== $this->getTenant($request)->id) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $request->validate([
            'payment_method' => ['required', 'in:cash,transfer,ewallet'],
            'paid' => ['required', 'numeric', 'min:0'],
        ]);

        if ($request->paid < $sale->total) {
            return response()->json(['message' => 'Pembayaran kurang dari total.'], 422);
        }

        DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'payment_method' => $request->payment_method,
                'paid' => $request->paid,
                'status' => Sale::STATUS_PAID,
            ]);
            $sale->recalculate();
            $sale->load('items.product');
            $sale->reduceStock();
        });

        return response()->json([
            'message' => 'Pembayaran berhasil.',
            'invoice_number' => $sale->invoice_number,
            'total' => (float) $sale->total,
            'paid' => (float) $sale->paid,
            'change' => (float) $sale->change,
        ]);
    }

    // ─── API: Struk ───────────────────────────────────────────────────────

    public function receipt(Request $request, Sale $sale): JsonResponse
    {
        if ($sale->tenant_id !== $this->getTenant($request)->id) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $sale->load('items', 'customer');

        return response()->json([
            'invoice_number' => $sale->invoice_number,
            'sale_date' => $sale->created_at->format('d/m/Y H:i'),
            'cashier' => Auth::guard('pos')->user()?->name,
            'customer' => $sale->customer?->name ?? 'Umum',
            'items' => $sale->items->map(fn($i) => [
                'name' => $i->product_name,
                'qty' => $i->qty,
                'price' => (float) $i->price,
                'subtotal' => (float) $i->subtotal,
            ]),
            'subtotal' => (float) $sale->subtotal,
            'total' => (float) $sale->total,
            'paid' => (float) $sale->paid,
            'change' => (float) $sale->change,
            'payment_method' => $sale->payment_method,
        ]);
    }
}
