<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     $credentials = $request->only('phone', 'password');

    //     // سعی در لاگین کاربر
    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['error' => 'نام کاربری یا رمز عبور اشتباه است.'], 401);
    //     }

    //     $user = Auth::user();

    //     // تشخیص نوع کاربر بر اساس مدل
    //     $userType = match (get_class($user)) {
    //         \App\Models\Admin::class => 'Admin',
    //         \App\Models\Doctor::class => 'Doctor',
    //         \App\Models\Client::class => 'Client',
    //         default => 'unknown',
    //     };

    //     // ساخت توکن با claim دلخواه
    //     $customClaims = [
    //         'user_type' => $userType,
    //         'user_id' => $user->id,
    //     ];

    //     $token = JWTAuth::claims($customClaims)->fromUser($user);

    //     return response()->json([
    //         'user' => $user,
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //     ]);
    // }

    public function adminLogin(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات ورود اشتباه است'], 401);
        }

        $user = Auth::guard('admin')->user();

        $token = JWTAuth::fromUser($user, [
            'user_type' => 'admin',
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function doctorLogin(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (!Auth::guard('doctor')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات ورود اشتباه است'], 401);
        }

        $user = Auth::guard('doctor')->user();

        $token = JWTAuth::fromUser($user, [
            'user_type' => 'doctor',
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function clientLogin(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (!Auth::guard('client')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات ورود اشتباه است'], 401);
        }

        $user = Auth::guard('client')->user();

        $token = JWTAuth::fromUser($user, [
            'user_type' => 'client',
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response(['message' => 'success'], 200);
    }

    public function getUserInfo()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }
}
