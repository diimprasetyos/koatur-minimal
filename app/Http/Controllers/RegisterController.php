<?php

namespace App\Http\Controllers;

use App\Models\Subscription\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Kalau sudah login, langsung ke admin
        if (Auth::check()) {
            return redirect('/admin');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'store_name'  => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'phone'       => ['nullable', 'string', 'max:20'],
        ]);

        try {
            DB::transaction(function () use ($validated, &$user) {
                // 1. Buat User
                $user = User::create([
                    'name'              => $validated['name'],
                    'email'             => $validated['email'],
                    'password'          => Hash::make($validated['password']),
                    'subscription_plan' => 'trial', // fallback lama, nanti bisa dihapus
                    'is_active'         => true,
                ]);

                // 2. Buat Tenant (toko)
                $tenant = Tenant::create([
                    'name'      => $validated['store_name'],
                    'phone'     => $validated['phone'] ?? null,
                    'is_active' => true,
                    // slug di-generate otomatis di boot() model
                ]);

                // 3. Attach user ke tenant via pivot (tabel tenant_user)
                $user->tenants()->attach($tenant->id);

                // 4. Set current_tenant_id supaya Filament langsung masuk toko yang benar
                $user->update(['current_tenant_id' => $tenant->id]);

                // 5. Assign role 'owner' ke user di tenant ini
                //    Pastikan role 'owner' sudah ada di tabel roles (via RoleSeeder)
                $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
                $user->assignRole($ownerRole);

                // 6. Buat Subscription trial 7 hari
                //    Method createTrial() ada di model Subscription
                Subscription::createTrial($user, trialDays: 10);
            });

            // 7. Login otomatis setelah register
            Auth::login($user);

            // 8. Redirect ke Filament admin panel
            return redirect('/admin')
                ->with('success', 'Selamat datang! Kamu punya 3 hari trial gratis.');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Terjadi kesalahan saat mendaftar. Coba lagi.']);
        }
    }
}
