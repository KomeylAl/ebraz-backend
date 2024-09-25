<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{

    public function getReferral($id) {
        $referral = Referral::query()->where('id', $id)->firstOrFail();
        $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->get();
        $doctor = User::query()->findOrFail($referral_user->where('role', 'doctor')[0]->user_id);
        $client = User::query()->findOrFail($referral_user->where('role', 'client')[1]->user_id);
        $payment = Payment::query()->where('referral_id', $referral->id)->firstOrFail();

        $result = [
            'referral_id' => $referral->id,
            'doctor' => $doctor->name,
            'client' => $client->name,
            'date' => $referral->date,
            'time' => $referral->time,
            'status' => $referral->status,
            'amount' => $referral->amount,
            'payment_status' => $payment->status,
            'payment' => $payment->amount
        ];

        return response()->json($result, 200);
        //return $referral;
    }

    public function getAllReferrals() {
        $referrals = Referral::query()->orderBy('date', 'desc')->get();

        $result = $referrals->map(function ($referral) {
            $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->get();
            $doctor = User::query()->findOrFail($referral_user->where('role', 'doctor')[0]->user_id);
            $client = User::query()->findOrFail($referral_user->where('role', 'client')[1]->user_id);
            $payment = Payment::query()->where('referral_id', $referral->id)->firstOrFail();
            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name,
                'client' => $client->name,
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status,
                'payment' => $referral->amount
            ];
        });

        return response()->json($result, 200);
    }

    public function getReferralByDate($date) {
        $referrals =  Referral::query()->where('date', $date)->get();
        $result = $referrals->map(function ($referral) {
            $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->get();
            $doctor = User::query()->findOrFail($referral_user->where('role', 'doctor')[0]->user_id);
            $client = User::query()->findOrFail($referral_user->where('role', 'client')[1]->user_id);
            $payment = Payment::query()->where('referral_id', $referral->id)->firstOrFail();
            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name,
                'client' => $client->name,
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status,
                'payment' => $payment->amount
            ];
        });
        return response()->json($result, 200);
    }

    public function addReferral(Request $request) {
        $request->validate([
            'doctor' => 'required',
            'client' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'required',
            'amount_status' => 'required',
            'time' => 'required'
        ],[
            'doctor.required' => 'فیلد پزشک الزامی است.',
            'client.required' => 'فیلد مراجع الزامی است.',
            'amount.required' => 'فیلد مبلغ جلسه الزامی است.',
            'date.required' => 'فیلد تاریخ جلسه الزامی است.',
            'status.required' => 'فیلد وضعیت الزامی است.',
            'time.required' => 'فیلد ساعت الزامی است.',
            'amount_status.required' => 'فیلد وضعیت پرداخت الزامی است.',
        ]);

        $appointment = Referral::query()->create([
            'date' => $request->date,
            'time' => $request->time,
            'amount' => $request->amount,
            'status' => $request->status
        ]);

        DB::table('referral_user')->insert(['referral_id' => $appointment->id, 'user_id' => $request->doctor, 'role' => 'doctor']);
        DB::table('referral_user')->insert(['referral_id' => $appointment->id, 'user_id' => $request->client, 'role' => 'client']);

        $amount = 0;
        if ($request->amount_status == 'paid') {
            $amount = $request->amount;
        }

        $payment = Payment::query()->create([
            'referral_id' => $appointment->id,
            'status' => $request->amount_status,
            'amount' => $amount
        ]);

        return response('successful', 201);
    }

    public function editReferral(Request $request, $id) {
        $request->validate([
            'doctor' => 'required',
            'client' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'required',
            'amount_status' => 'required',
            'time' => 'required'
        ],[
            'doctor.required' => 'فیلد پزشک الزامی است.',
            'client.required' => 'فیلد مراجع الزامی است.',
            'amount.required' => 'فیلد مبلغ جلسه الزامی است.',
            'date.required' => 'فیلد تاریخ جلسه الزامی است.',
            'status.required' => 'فیلد وضعیت الزامی است.',
            'time.required' => 'فیلد ساعت الزامی است.',
            'amount_status.required' => 'فیلد وضعیت پرداخت الزامی است.',
        ]);

        DB::table('referrals')->where('id', $id)->update([
            'date' => $request->date,
            'time' => $request->time,
            'amount' => $request->amount,
            'status' => $request->status
        ]);

        $referral = Referral::query()->where('id', $id)->firstOrFail();

        $referral_doctor = DB::table('referral_user')
            ->where('referral_id', $referral->id)->where('role', 'doctor')->update([
            'user_id' => $request->doctor]);

        $referral_client = DB::table('referral_user')
            ->where('referral_id', $referral->id)->where('role', 'client')->update([
                'user_id' => $request->client]);

        $payment = DB::table('payments')->where('referral_id', $referral->id)->update([
            'status' => $request->amount_status,
            'amount' => $request->amount
        ]);

        return response('successful', 200);
    }

    public function deleteReferral($id) {
        Referral::query()->where('id', $id)->delete();
        return response('success', 200);
    }
}
