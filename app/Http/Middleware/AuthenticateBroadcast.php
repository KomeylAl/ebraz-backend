<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Client;

class AuthenticateBroadcast
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // ✅ دریافت توکن از Authorization یا query string یا header سفارشی
            $bearer = $request->bearerToken();
            $headerToken = $request->headers->get('token');
            $token = $bearer ?? $headerToken ?? $request->query('token');

            \Log::info('Broadcast Auth Token', ['token' => $token]);

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            // ✅ اعتبارسنجی JWT
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();


            $userId = $payload->get('user_id');
            $userType = $payload->get('user_type');

            // فرض می‌کنیم فعلاً فقط admin داریم (می‌تونی براساس payload تنظیم کنی)
            $user = match ($userType) {
                'Admin' => Admin::find($userId),
                'Doctor' => Doctor::find($userId),
                'Client' => Client::find($userId),
                default => null,
            };


            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            \Log::info('Broadcast User:', ['user_type' => $payload]);
            
            $rr = $request->setUserResolver(fn() => $user);
            $auth = auth()->setUser($user);
            
            \Log::info('Broadcast User:', ['auth' => $rr]);
            return $next($request);
        } catch (\Exception $e) {
            \Log::error('Broadcasting auth failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}