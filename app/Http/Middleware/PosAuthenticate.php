<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PosAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek autentikasi
        if (!Auth::guard('pos')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('pos.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek tenant sudah dipilih di session
        $tenantId = session('pos_tenant_id');

        if (!$tenantId) {
            // User sudah login tapi belum pilih toko (multi-tenant)
            return redirect()->route('pos.select-tenant');
        }

        // 3. Validasi: tenant benar-benar milik user ini dan masih aktif
        $user = Auth::guard('pos')->user();
        $tenant = $user->tenants()
            ->where('tenants.is_active', true)
            ->where('tenants.id', $tenantId)
            ->first();

        if (!$tenant) {
            // Tenant tidak valid / dicabut aksesnya → hapus session, suruh pilih ulang
            session()->forget('pos_tenant_id');

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tenant tidak valid.'], 403);
            }

            return redirect()->route('pos.select-tenant')
                ->with('error', 'Sesi toko tidak valid. Silakan pilih toko kembali.');
        }

        // 4. Inject tenant ke request agar mudah diakses di controller
        $request->merge(['_pos_tenant' => $tenant]);

        // Opsional: share ke semua view
        view()->share('posTenant', $tenant);

        return $next($request);
    }
}
