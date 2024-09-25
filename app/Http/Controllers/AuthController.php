<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('phone', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'نام کاربری یا رمز عبور اشتباه است.'], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout() {
        Auth::logout();
        return response([], 200);
    }

    public function getUserInfo() {
        $user = Auth::user();
        return response()->json($user, 200);
    }
}
