<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Halaman Utama Kasir
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        $tenant = Auth::guard('pos')->user()?->currentTenant;

        $categories = Category::query()
            ->where('tenant_id', $tenant?->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'color']);

        return view('pos.index', compact('categories', 'tenant'));
    }

    /*
    |--------------------------------------------------------------------------
    | JSON API: Ambil Produk
    |--------------------------------------------------------------------------
    */

    public function products(Request $request): JsonResponse
    {
        $tenant = Auth::guard('pos')->user()?->currentTenant;

        $products = Product::query()
            ->where('tenant_id', $tenant?->id)
            ->where('is_active', true)
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'price', 'stock', 'track_stock', 'image', 'category_id']);

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'uuid' => $p->uuid,
            'name' => $p->name,
            'price' => (float) $p->price,
            'stock' => $p->track_stock ? $p->stock : null,
            'in_stock' => $p->isInStock(),
            'image' => $p->image ? asset('storage/' . $p->image) : null,
            'category_id' => $p->category_id,
        ]));
    }

    /*
    |--------------------------------------------------------------------------
    | JSON API: Buat Sale Baru (draft)
    |--------------------------------------------------------------------------
    */

    public function createSale(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['sometimes', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = Auth::guard('pos')->user()?->currentTenant;
        $user = Auth::guard('pos')->user();

        $sale = DB::transaction(function () use ($request, $tenant, $user) {
            $sale = Sale::create([
                'tenant_id' => $tenant?->id,
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
                $product = Product::findOrFail($item['product_id']);

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

            $sale->refresh();

            return $sale;
        });

        return response()->json([
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'subtotal' => (float) $sale->subtotal,
            'total' => (float) $sale->total,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JSON API: Proses Pembayaran
    |--------------------------------------------------------------------------
    */

    public function processSale(Request $request, Sale $sale): JsonResponse
    {
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

    /*
    |--------------------------------------------------------------------------
    | JSON API: Data Struk
    |--------------------------------------------------------------------------
    */

    public function receipt(Sale $sale): JsonResponse
    {
        $sale->load('items', 'customer');

        return response()->json([
            'invoice_number' => $sale->invoice_number,
            'sale_date' => $sale->sale_date->format('d/m/Y H:i'),
            'cashier' => Auth::guard('pos')->user()?->name,
            'customer' => $sale->customer?->name ?? 'Umum',
            'items' => $sale->items->map(fn($i) => [
                'name' => $i->product_name,
                'qty' => $i->qty,
                'price' => (float) $i->price,
                'discount' => (float) $i->discount,
                'subtotal' => (float) $i->subtotal,
            ]),
            'subtotal' => (float) $sale->subtotal,
            'discount' => (float) $sale->discount,
            'tax' => (float) $sale->tax,
            'total' => (float) $sale->total,
            'paid' => (float) $sale->paid,
            'change' => (float) $sale->change,
            'payment_method' => $sale->payment_method,
        ]);
    }
}
