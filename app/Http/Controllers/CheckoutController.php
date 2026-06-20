<?php

namespace App\Http\Controllers;

use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionInvoice;
use App\Models\Subscription\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CheckoutController extends Controller
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', Password::min(8)],
            'phone'    => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $plan) {
                $user = User::where('email', $validated['email'])->first();

                if ($user) {
                    // Email sudah terdaftar -> anggap user lama, validasi password.
                    if (!Hash::check($validated['password'], $user->password)) {
                        throw ValidationException::withMessages([
                            'password' => 'Email ini sudah terdaftar. Masukkan password yang benar untuk melanjutkan.',
                        ]);
                    }
                } else {
                    $user = User::create([
                        'name'              => $validated['name'],
                        'email'             => $validated['email'],
                        'password'          => Hash::make($validated['password']),
                        'subscription_plan' => $plan->slug,
                        'is_active'         => true,
                    ]);

                    // Tenant dengan nama default, bisa diubah nanti dari admin.
                    $tenant = Tenant::create([
                        'name'      => $validated['name'] . ' Store',
                        'phone'     => $validated['phone'] ?? null,
                        'is_active' => true,
                    ]);

                    $user->tenants()->attach($tenant->id);
                    $user->update(['current_tenant_id' => $tenant->id]);

                    $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
                    $user->assignRole($ownerRole);
                }

                // Subscription baru dengan status pending -> belum boleh akses admin.
                $subscription = Subscription::create([
                    'user_id'              => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status'               => Subscription::STATUS_PENDING,
                ]);

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

                return [$user, $invoice];
            });
        } catch (ValidationException $e) {
            return back()->withInput($request->except('password'))->withErrors($e->errors());
        }

        [$user, $invoice] = $result;

        $xenditResponse = $this->createXenditInvoice($invoice, $user, $plan);

        if (!$xenditResponse) {
            return back()
                ->withInput($request->except('password'))
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

    private function createXenditInvoice(SubscriptionInvoice $invoice, User $user, SubscriptionPlan $plan): ?array
    {
        $secretKey = config('xendit.secret_key');

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id'          => $invoice->invoice_number,
                    'amount'               => $invoice->amount,
                    'payer_email'          => $user->email,
                    'description'          => 'Langganan ' . $plan->name . ' — ' . config('app.name'),
                    'success_redirect_url' => route('checkout.success'),
                    'failure_redirect_url' => route('checkout.failed'),
                    'invoice_duration'     => 86400,
                    'currency'             => 'IDR',
                    'customer'             => array_filter([
                        'given_names'   => $user->name,
                        'email'         => $user->email,
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

            Log::error('Checkout: Xendit failed', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Checkout: exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
