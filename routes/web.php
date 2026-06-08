<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/', function () {
    return view('landing.index');
})->name('home');

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/auth/sso', function (Request $request) {
    $token = $request->query('token');
    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
        return redirect(env('FRONTEND_URL') . '/login?error=invalid_token');
    }

    Auth::login($accessToken->tokenable);

    return redirect('/admin');
})->middleware('web');

Route::get('/reports/export', [ReportExportController::class, 'export'])
    ->name('reports.export')
    ->middleware(['auth', 'signed']);

// Route register publik (bukan Filament)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Route billing (harus login, tidak perlu subscription aktif supaya yang expired bisa bayar)
Route::middleware('auth')->group(function () {
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
});

// Webhook Xendit — tidak pakai session/csrf
Route::post('/webhook/xendit', [BillingController::class, 'xenditWebhook'])
    ->name('webhook.xendit')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


Route::get('/pricing', function () {
    $plans = \App\Models\Subscription\SubscriptionPlan::active()
        ->where('slug', '!=', 'trial')
        ->orderBy('price')
        ->get();

    return view('landing.pricing', compact('plans'));
})->name('pricing');

// xendit test flow
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/success', [\App\Http\Controllers\GuestCheckoutController::class, 'success'])->name('success');
    Route::get('/failed',  [\App\Http\Controllers\GuestCheckoutController::class, 'failed'])->name('failed');
    Route::get('/{plan}',  [\App\Http\Controllers\GuestCheckoutController::class, 'show'])->name('show');
    Route::post('/{plan}', [\App\Http\Controllers\GuestCheckoutController::class, 'process'])->name('process');
});
