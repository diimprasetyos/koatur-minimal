<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/', function () {
    return view('landing.index');
});

Route::get('/auth/sso', function (Request $request) {
    $token = $request->query('token');
    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
        return redirect(env('FRONTEND_URL') . '/login?error=invalid_token');
    }

    Auth::login($accessToken->tokenable);

    return redirect('/admin');
})->middleware('web');
