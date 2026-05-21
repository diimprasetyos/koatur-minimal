<?php

namespace App\Http\Controllers;

use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionInvoice;
use App\Models\Subscription\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    /**
     * Tampilkan halaman billing.
     */
    public function index()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->first();

        $plans = SubscriptionPlan::active()
            ->where('slug', '!=', 'trial')
            ->orderBy('price')
            ->get();

        $invoices = $user->subscriptions()
            ->with(['invoices' => fn($q) => $q->latest()->limit(10)])
            ->get()
            ->pluck('invoices')
            ->flatten();

        return view('billing.index', compact('user', 'subscription', 'plans', 'invoices'));
    }

    /**
     * Buat invoice Xendit dan redirect user ke halaman pembayaran.
     *
     * Flow:
     * 1. User klik "Pilih Plan" di /billing
     * 2. Controller buat invoice lokal (status: pending)
     * 3. Kirim request ke Xendit API → dapat invoice_url
     * 4. Redirect user ke invoice_url (halaman bayar Xendit)
     * 5. Setelah bayar, Xendit kirim webhook → kita aktifkan subscription
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan_slug' => ['required', 'exists:subscription_plans,slug'],
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::where('slug', $validated['plan_slug'])->firstOrFail();

        // Ambil subscription yang ada atau buat baru
        $subscription = $user->subscriptions()
            ->whereIn('status', [Subscription::STATUS_TRIAL, Subscription::STATUS_EXPIRED])
            ->latest()
            ->first();

        if (!$subscription) {
            $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();
            $subscription = Subscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $trialPlan->id,
                'status'               => Subscription::STATUS_TRIAL,
                'started_at'           => now(),
                'expires_at'           => now()->addDays(7),
            ]);
        }

        // Update ke plan yang dipilih user
        $subscription->update(['subscription_plan_id' => $plan->id]);

        // Buat invoice lokal
        $invoiceNumber = SubscriptionInvoice::generateInvoiceNumber();

        $invoice = SubscriptionInvoice::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $user->id,
            'invoice_number'  => $invoiceNumber,
            'amount'          => $plan->price,
            'status'          => SubscriptionInvoice::STATUS_PENDING,
            'payment_method'  => 'xendit',
            'period_start'    => now(),
            'period_end'      => now()->addDays(30),
        ]);

        // Hit Xendit API
        $xenditResponse = $this->createXenditInvoice($invoice, $user, $plan);

        if (!$xenditResponse) {
            return back()->withErrors(['checkout' => 'Gagal membuat halaman pembayaran. Coba lagi.']);
        }

        // Simpan Xendit invoice ID dan URL ke invoice lokal
        $invoice->update([
            'payment_token' => $xenditResponse['id'],
            'payment_url'   => $xenditResponse['invoice_url'],
        ]);

        // Redirect ke halaman bayar Xendit
        return redirect($xenditResponse['invoice_url']);
    }

    /**
     * Kirim request ke Xendit Invoice API.
     * Docs: https://developers.xendit.co/api-reference/#create-invoice
     */
    private function createXenditInvoice(SubscriptionInvoice $invoice, $user, SubscriptionPlan $plan): ?array
    {
        $secretKey = config('xendit.secret_key');

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id'          => $invoice->invoice_number,
                    'amount'               => $invoice->amount,
                    'payer_email'          => $user->email,
                    'description'          => 'Langganan ' . $plan->name . ' — ' . config('app.name'),
                    'success_redirect_url' => route('billing') . '?status=success',
                    'failure_redirect_url' => route('billing') . '?status=failed',
                    'invoice_duration'     => 86400, // 24 jam sebelum expire
                    'currency'             => 'IDR',
                    'customer' => array_filter([
                        'given_names'   => $user->name,
                        'email'         => $user->email,
                        'mobile_number' => $user->phone ?: null,
                    ]),
                    'items' => [
                        [
                            'name'     => 'Langganan ' . $plan->name,
                            'quantity' => 1,
                            'price'    => $plan->price,
                            'category' => 'Subscription',
                        ],
                    ],
                    // Uncomment untuk batasi metode pembayaran:
                    // 'payment_methods' => ['BCA', 'BNI', 'BRI', 'MANDIRI', 'OVO', 'DANA', 'GOPAY', 'QRIS'],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit invoice creation failed', [
                'status'   => $response->status(),
                'response' => $response->json(),
                'invoice'  => $invoice->invoice_number,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit API exception', [
                'message' => $e->getMessage(),
                'invoice' => $invoice->invoice_number,
            ]);

            return null;
        }
    }

    /**
     * Webhook dari Xendit — otomatis dipanggil setelah user bayar.
     *
     * Setup di Xendit Dashboard:
     *   Settings → Webhooks → Invoice Paid
     *   URL: https://yourdomain.com/webhook/xendit
     *
     * Xendit kirim header x-callback-token yang harus cocok dengan
     * XENDIT_WEBHOOK_TOKEN di .env kamu.
     */
    public function xenditWebhook(Request $request)
    {
        // Verifikasi token keamanan dari Xendit
        $callbackToken = $request->header('x-callback-token');
        $expectedToken = config('xendit.webhook_token');

        if ($callbackToken !== $expectedToken) {
            Log::warning('Xendit webhook: invalid callback token');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $externalId = $request->input('external_id'); // = invoice_number kita
        $status     = $request->input('status');       // PAID | SETTLED | EXPIRED

        Log::info('Xendit webhook received', compact('externalId', 'status'));

        // Cari invoice lokal
        $invoice = SubscriptionInvoice::where('invoice_number', $externalId)
            ->with('subscription.plan', 'subscription.user')
            ->first();

        if (!$invoice) {
            Log::warning('Xendit webhook: invoice not found', compact('externalId'));
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        if (in_array($status, ['PAID', 'SETTLED'])) {
            // Tandai invoice lunas
            $invoice->markAsPaid($request->all());

            // Aktifkan subscription 30 hari
            $invoice->subscription->activate(30, $externalId);

            // Sync field lama (backward compat dengan PlanLimit)
            $invoice->subscription->user->update([
                'subscription_plan' => $invoice->subscription->plan->slug,
            ]);

            Log::info('Subscription activated via Xendit', [
                'user_id' => $invoice->user_id,
                'plan'    => $invoice->subscription->plan->slug,
                'expires' => $invoice->subscription->fresh()->expires_at,
            ]);
        } elseif ($status === 'EXPIRED') {
            $invoice->update(['status' => SubscriptionInvoice::STATUS_FAILED]);
            Log::info('Xendit invoice expired', compact('externalId'));
        }

        return response()->json(['message' => 'OK']);
    }
}
