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
            'phone'       => ['required', 'string', 'max:20'],
        ]);

        try {
            DB::transaction(function () use ($validated, &$user) {
                // buat User
                $user = User::create([
                    'name'              => $validated['name'],
                    'email'             => $validated['email'],
                    'password'          => Hash::make($validated['password']),
                    'subscription_plan' => 'trial',
                    'is_active'         => true,
                ]);

                // buat Tenant (toko)
                $tenant = Tenant::create([
                    'name'      => $validated['store_name'],
                    'phone'     => $validated['phone'],
                    'is_active' => true,
                    // slug di-generate otomatis di boot() model
                ]);

                // attach user ke tenant via pivot (tabel tenant_user)
                $user->tenants()->attach($tenant->id);

                // set current_tenant_id supaya Filament langsung masuk toko yang benar
                $user->update(['current_tenant_id' => $tenant->id]);

                // assign role 'owner' ke user di tenant ini
                $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
                $user->assignRole($ownerRole);

                // buat subscription trial duration
                Subscription::createTrial($user, trialDays: 3);
            });

            // login setelah register
            Auth::login($user);

            // redirect ke Filament admin panel
            return redirect('/admin')
                ->with('success', 'Selamat datang! Kamu punya 3 hari trial gratis.');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Terjadi kesalahan saat mendaftar. Coba lagi.']);
        }
    }
}
