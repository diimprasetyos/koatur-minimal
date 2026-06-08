<?php

namespace App\Http\Controllers;

use App\Models\Subscription\SubscriptionInvoice;
use App\Models\Subscription\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GuestCheckoutController
 *
 * Public checkout flow tanpa login — dibuat khusus untuk:
 *  - Verifikasi KYC Xendit (reviewer bisa test tanpa akun)
 *  - Calon customer yang belum daftar tapi ingin lihat halaman bayar
 *
 * Flow:
 *  GET  /checkout/{plan}   → form isi nama & email
 *  POST /checkout/{plan}   → buat Xendit invoice → redirect ke payment URL
 *  GET  /checkout/success  → halaman sukses
 *  GET  /checkout/failed   → halaman gagal
 *
 * CATATAN: Invoice yang dibuat di sini tidak terikat ke User/Subscription manapun
 * (kolom user_id & subscription_id nullable). Ini murni untuk demo/KYC flow.
 */
class GuestCheckoutController extends Controller
{
    public function show(string $planSlug)
    {
        $plan = SubscriptionPlan::active()
            ->where('slug', $planSlug)
            ->where('slug', '!=', 'trial')
            ->firstOrFail();

        return view('checkout.guest', compact('plan'));
    }

    public function process(Request $request, string $planSlug)
    {
        $plan = SubscriptionPlan::active()
            ->where('slug', $planSlug)
            ->where('slug', '!=', 'trial')
            ->firstOrFail();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $invoiceNumber = SubscriptionInvoice::generateInvoiceNumber();

        $invoice = SubscriptionInvoice::create([
            'subscription_id' => null,
            'user_id'         => null,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $plan->price,
            'status'          => SubscriptionInvoice::STATUS_PENDING,
            'payment_method'  => 'xendit',
            'period_start'    => now(),
            'period_end'      => now()->addDays(30),
        ]);

        $xenditResponse = $this->createXenditInvoice($invoice, $validated, $plan);

        if (!$xenditResponse) {
            return back()
                ->withInput()
                ->withErrors(['checkout' => 'Gagal membuat halaman pembayaran. Silakan coba lagi.']);
        }

        $invoice->update([
            'payment_token' => $xenditResponse['id'],
            'payment_url'   => $xenditResponse['invoice_url'],
        ]);

        return redirect($xenditResponse['invoice_url']);
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function failed()
    {
        return view('checkout.failed');
    }

    private function createXenditInvoice(SubscriptionInvoice $invoice, array $customer, SubscriptionPlan $plan): ?array
    {
        $secretKey = config('xendit.secret_key');

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id'          => $invoice->invoice_number,
                    'amount'               => $invoice->amount,
                    'payer_email'          => $customer['email'],
                    'description'          => 'Langganan ' . $plan->name . ' — ' . config('app.name'),
                    'success_redirect_url' => route('checkout.success'),
                    'failure_redirect_url' => route('checkout.failed'),
                    'invoice_duration'     => 86400,
                    'currency'             => 'IDR',
                    'customer'             => array_filter([
                        'given_names'   => $customer['name'],
                        'email'         => $customer['email'],
                        'mobile_number' => $customer['phone'] ?? null,
                    ]),
                    'items' => [[
                        'name'     => 'Langganan ' . $plan->name . ' (1 bulan)',
                        'quantity' => 1,
                        'price'    => $plan->price,
                        'category' => 'Subscription',
                    ]],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GuestCheckout: Xendit failed', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('GuestCheckout: exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
