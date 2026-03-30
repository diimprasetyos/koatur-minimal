<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        // Set current_tenant
        if (!$user->current_tenant_id) {
            $firstTenant = $user->tenants()->first();
            if ($firstTenant) {
                $user->update(['current_tenant_id' => $firstTenant->id]);
            }
        }

        $token = $user->createToken('nextjs-client')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->only('id', 'uuid', 'name', 'email'),
            'current_tenant' => $user->currentTenant?->only('id', 'uuid', 'name', 'slug'),
            'tenants' => $user->tenants()->get()->map->only('id', 'uuid', 'name', 'slug'),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('currentTenant');

        return response()->json([
            'user' => $user->only('id', 'uuid', 'name', 'email'),
            'current_tenant' => $user->currentTenant?->only('id', 'uuid', 'name', 'slug'),
            'tenants' => $user->tenants()->get()->map->only('id', 'uuid', 'name', 'slug'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function switchTenant(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $hasAccess = $request->user()
            ->tenants()
            ->where('tenants.id', $request->tenant_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $request->user()->update(['current_tenant_id' => $request->tenant_id]);

        return response()->json([
            'message' => 'Tenant berhasil diganti',
            'current_tenant' => $request->user()->currentTenant?->only('id', 'uuid', 'name', 'slug'),
        ]);
    }
}
