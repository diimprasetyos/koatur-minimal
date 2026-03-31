<?php

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