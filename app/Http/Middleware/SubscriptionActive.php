<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Belum login: biarkan middleware 'auth' yang redirect ke login page
        if (!$user) {
            return $next($request);
        }

        // Super admin tidak kena pembatasan subscription
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Cek apakah user punya subscription yang masih aktif (termasuk trial)
        if (!$user->hasActiveSubscription()) {
            // Kalau request adalah AJAX/JSON (misalnya Livewire), kembalikan 402
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Subscription kamu sudah habis. Silakan perpanjang.',
                ], 402);
            }

            // Redirect ke halaman billing dengan pesan
            return redirect()->route('billing')
                ->with('warning', 'Masa langgananmu sudah berakhir. Silakan pilih plan untuk melanjutkan.');
        }

        // Subscription masih aktif — lanjutkan request
        return $next($request);
    }
}
