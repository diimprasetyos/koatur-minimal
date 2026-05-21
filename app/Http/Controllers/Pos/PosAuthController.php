<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class PosAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pos.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('pos')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        $user = Auth::guard('pos')->user();
        $tenants = $user->tenants()->where('tenants.is_active', true)->get();

        // Tidak punya tenant sama sekali
        if ($tenants->isEmpty()) {
            Auth::guard('pos')->logout();
            return back()->withErrors(['email' => 'Akun Anda belum terdaftar di toko manapun.']);
        }

        // Hanya 1 tenant → langsung set dan masuk
        if ($tenants->count() === 1) {
            session(['pos_tenant_id' => $tenants->first()->id]);
            return redirect()->intended(route('pos.index'));
        }

        // Lebih dari 1 tenant → tampilkan halaman pilih toko
        return redirect()->route('pos.select-tenant');
    }

    // Pilih Tenant (hanya muncul jika user punya > 1 tenant)

    public function showSelectTenant(): View|RedirectResponse
    {
        $user = Auth::guard('pos')->user();
        $tenants = $user->tenants()->where('tenants.is_active', true)->get();

        if ($tenants->isEmpty()) {
            Auth::guard('pos')->logout();
            return redirect()->route('pos.login')
                ->withErrors(['email' => 'Tidak ada toko aktif.']);
        }

        // Kalau cuma 1, tidak perlu pilih
        if ($tenants->count() === 1) {
            session(['pos_tenant_id' => $tenants->first()->id]);
            return redirect()->route('pos.index');
        }

        return view('pos.auth.select-tenant', compact('tenants'));
    }

    public function selectTenant(Request $request): RedirectResponse
    {
        $request->validate([
            'tenant_id' => ['required', 'integer'],
        ]);

        $user = Auth::guard('pos')->user();
        $tenant = $user->tenants()
            ->where('tenants.is_active', true)
            ->where('tenants.id', $request->tenant_id)
            ->first();

        if (!$tenant) {
            return back()->withErrors(['tenant_id' => 'Toko tidak valid atau tidak aktif.']);
        }

        session(['pos_tenant_id' => $tenant->id]);

        return redirect()->route('pos.index');
    }

    // Switch Tenant (dari dalam kasir, tanpa logout)

    public function switchTenant(Request $request): RedirectResponse
    {
        $request->validate(['tenant_id' => ['required', 'integer']]);

        $user = Auth::guard('pos')->user();
        $tenant = $user->tenants()
            ->where('tenants.is_active', true)
            ->where('tenants.id', $request->tenant_id)
            ->first();

        if (!$tenant) {
            return back()->withErrors(['tenant_id' => 'Toko tidak valid.']);
        }

        session(['pos_tenant_id' => $tenant->id]);

        return redirect()->route('pos.index')
            ->with('success', 'Berhasil pindah ke toko ' . $tenant->name);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('pos')->logout();

        $request->session()->forget('pos_tenant_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pos.login');
    }
}
