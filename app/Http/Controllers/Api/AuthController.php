<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        // JWT attempt (api guard)
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    if (!Auth::guard('web')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        // Fetch user
        $user = User::where('email', $request->email)->first();

        // Web session login explicitly
        Auth::guard('web')->login($user);

        // Get role
        $role = $user->getRoleNames()->first();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 1440,
            'user' => $user,
            'role' => $role
        ]);
    }

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout(Request $request)
    {
        // Logout both guards
        Auth::guard('api')->logout();
        Auth::guard('web')->logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
