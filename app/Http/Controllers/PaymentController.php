<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function getAllPayments() {
        $payments = Payment::query()->orderBy('created_at', 'desc')->get();
        $result = $payments->map(function ($payment) {
            $referral = Referral::query()->where('id', $payment->referral_id)->firstOrFail();
            $referral_client = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('role', 'client')->first();
            $referral_doctor= DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('role', 'doctor')->first();
            $client= User::query()
                ->where('id', $referral_client->user_id)->firstOrFail();
            $doctor= User::query()
                ->where('id', $referral_doctor->user_id)->firstOrFail();
            return [
                'id' => $payment->id,
                'client' => $client->name,
                'doctor' => $doctor->name,
                'referral_date' => $referral->date,
                'amount' => $payment->amount,
                'status' => $payment->status
            ];
        });
        return response()->json($result, 200);
    }
}
