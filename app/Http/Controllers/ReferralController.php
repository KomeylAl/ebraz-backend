<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Http\Resources\ReferralCollection;
use App\Http\Resources\ReferralResource;
use Notification;


class ReferralController extends Controller
{

    public function getReferral($id)
    {
        $referral = Referral::query()->where('id', $id)->firstOrFail();
        $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->first();
        $doctor = Doctor::query()->findOrFail($referral_user->doctor_id);
        $client = Client::query()->where('id', $referral_user->client_id)->first();
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

    // public function getAllReferrals() {
    //     $referrals = Referral::query()->orderBy('created_at', 'desc')->get();

    //     $result = $referrals->map(function ($referral) {
    //         $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->first();
    //         $payment = Payment::query()->where('referral_id', $referral->id)->first();
    //         $doctor = Doctor::query()->findOrFail($referral_user->doctor_id);
    //         $client = Client::query()->where('id', $referral_user->client_id)->first();
    //         return [
    //             'referral_id' => $referral->id,
    //             'doctor' => $doctor->name,
    //             'client' => $client->name,
    //             'date' => $referral->date,
    //             'time' => $referral->time,
    //             'status' => $referral->status,
    //             'amount' => $referral->amount,
    //             'payment_status' => $payment->status,
    //             'payment' => $referral->amount
    //         ];
    //     });

    //     return response()->json($result, 200);
    // }

    public function index(Request $request)
    {
        $query = Referral::with(['client', 'doctor']);

        // فیلتر بر اساس client_id
        if ($request->filled('client_id')) {
            $client_id = $request->query('client_id');
            $query->whereHas('client', function ($q) use ($client_id) {
                $q->where('clients.id', $client_id);
            });
        }

        // جستجو بر اساس نام یا تلفن
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['id', 'created_at', 'amount', 'status'];
        $sortBy = $request->query('sort_by', 'created_at');
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $sortDirection = strtolower($request->query('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        $perPage = max(1, min((int) $request->query('per_page', 10), 100));
        $referrals = $query->paginate($perPage);

        return ReferralResource::collection($referrals);
    }

    public function getAllReferrals(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search', '');
        $date = $request->query('date', '');
        $client_id = $request->query('client_id', '');

        $query = Referral::query()
            ->with(['doctor', 'client', 'payment'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('client', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('doctor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->when($client_id, function ($q) use ($client_id) {
                $q->whereHas('client', function ($q2) use ($client_id) {
                    $q2->where('clients.id', $client_id);
                });
            })
            ->orderBy('created_at', 'desc');

        $referrals = $query->paginate($perPage);

        return new ReferralCollection($referrals);
    }

    public function getReferralByDate($date)
    {
        $referrals = Referral::query()->where('date', $date)->get();
        $result = $referrals->map(function ($referral) {
            $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->first();
            $doctor = Doctor::query()->findOrFail($referral_user->doctor_id);
            $client = Client::query()->findOrFail($referral_user->client_id);
            $payment = Payment::query()->where('referral_id', $referral->id)->first();
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

    public function addReferral(Request $request)
    {
        $request->validate([
            'doctor' => 'required',
            'client' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'required',
            'amount_status' => 'required',
            'time' => 'required'
        ], [
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

        DB::table('referral_user')
            ->insert(['referral_id' => $appointment->id, 'doctor_id' => $request->doctor, 'client_id' => $request->client]);


        $app_user = DB::table('referral_user')->where('referral_id', $appointment->id)->first();

        $amount = 0;
        if ($request->amount_status == 'paid') {
            $amount = $request->amount;
        }

        $payment = Payment::query()->create([
            'referral_id' => $appointment->id,
            'status' => $request->amount_status,
            'amount' => $amount
        ]);


        $client = Client::query()->where('id', $app_user->client_id)->first();
        $doctor = Doctor::query()->where('id', $app_user->doctor_id)->first();
        $carbonDate = Carbon::createFromFormat('Y-m-d', $appointment->date);
        $jalaliDate = Jalalian::fromCarbon($carbonDate)->format('d M Y');

        // $notification = \App\Models\Notification::create([
        //     'title' => 'ثبت نوبت',
        //     'message' => "نوبت مراجع {$client->name} در تاریخ {$jalaliDate} با مشاور {$doctor->name} ثبت شد.",
        //     'type' => 'appointment',
        //     'priority' => 'low',
        //     'notifiable_type' => \App\Models\Admin::class,
        //     'notifiable_id' => null,
        // ]);

        // event(new NotificationCreated($notification));

        $paymentText = $request->amount_status == "unpaid" ? "لطفا مبلغ جلسه {$request->amount} را در اسرع وقت پرداخت کنید." : null;
        $text = "کلینیک ابراز\n{$client->name} عزیز\nنوبت شما در تاریخ {$jalaliDate} ساعت {$appointment->time} ثبت گردید.\n{$paymentText}";

        $data = [
            "lineNumber" => "9982005424",
            "messageText" => $text,
            "mobiles" => [$client->phone]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sms.ir/v1/send/bulk',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'X-API-KEY: zkogPOvFAhdiDljYFkHMlAe4poPkaOGup1YzVK7NnHqplwCD',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        // $error = curl_error($curl);
        curl_close($curl);
        // echo $response;
        if (curl_error($curl)) {
            return response()->json(curl_error($curl), 500);
        }

        return response('success', 201);
    }

    public function editReferral(Request $request, $id)
    {
        $request->validate([
            'doctor' => 'required',
            'client' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'required',
            'amount_status' => 'required',
            'time' => 'required'
        ], [
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

        DB::table('referral_user')
            ->where('referral_id', $referral->id)->update([
                    'doctor_id' => $request->doctor,
                    'client_id' => $request->client
                ]);

        $referral_user = DB::table('referral_user')->where('referral_id', $referral->id)->first();

        $payment = DB::table('payments')->where('referral_id', $referral->id)->update([
            'status' => $request->amount_status,
            'amount' => $request->amount
        ]);

        return response('successful', 200);
    }

    public function deleteReferral($id)
    {
        Referral::query()->where('id', $id)->delete();
        return response('success', 200);
    }
}
