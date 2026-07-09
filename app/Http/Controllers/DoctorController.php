<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Referral;
use App\Models\Payment;
use App\Models\Client;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\ReferralResource;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['departments', 'resumeRecord']);

        // جستجو
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فیلتر بر اساس تاریخ تولد (اختیاری)
        if ($request->filled('birth_date')) {
            $query->whereDate('birth_date', $request->query('birth_date'));
        }

        // مرتب سازی
        $sortBy = $request->query('sort_by', 'created_at'); // پیشفرض بر اساس created_at
        $sortDirection = $request->query('sort_direction', 'desc'); // پیشفرض نزولی
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $doctors = $query->paginate($perPage);

        // ریترن به فرمت Resource
        return DoctorResource::collection($doctors);
    }

    public function show(Doctor $doctor)
    {
        return new DoctorResource($doctor->load(['resumeRecord', 'departments', 'resources']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|min:11|unique:doctors',
            'national_code' => 'required|string|max:10|min:10|unique:doctors',
            'card_number' => 'string|max:16|min:16|unique:doctors',
            'medical_number' => 'string|max:16|unique:doctors',
            'birth_date' => 'required',
            'days' => 'string|nullable',
            'department_ids' => 'array',
            'department_ids.*' => 'exists:departments,id',
            'email' => 'string|email|unique:doctors',
            'avatar' => 'nullable|image|max:2048',
            'resume' => 'nullable|file|mimetypes:application/pdf|max:2048',
        ], [
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.min' => 'تلفن نمی تواند کمتر از 11 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'national_code.unique' => 'این کد ملی قبلا ثبت شده است.',
            'national_code.required' => 'فیلد کد ملی الزامی است.',
            'national_code.max' => 'کد ملی نمی تواند بیشتر از 10 کاراکتر باشد.',
            'national_code.min' => 'کد ملی نمی تواند کمتر از 10 کاراکتر باشد.',
            'card_number.unique' => 'این شماره کارت قبلا ثبت شده است.',
            'card_number.max' => 'شماره کارت نمی تواند بیشتر از 16 کاراکتر باشد.',
            'card_number.min' => 'شماره کارت نمی تواند کمتر از 16 کاراکتر باشد.',
            'medical_number.unique' => 'این شماره نظام روانشناسی قبلا ثبت شده است.',
            'medical_number.max' => 'شماره نظام روانشناسی نمی تواند بیشتر از 16 کاراکتر باشد.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
            'email.email' => 'فرمت ایمیل صحیح نیست',
            'email.unique' => 'ایمیل نمی تواند تکراری باشد',
        ]);

        $data = $validated;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctor_avatars', 'public');
        }

        if ($request->hasFile('resume')) {
            $data['resume'] = $request->file('resume')->store('doctor_resumes', 'public');
        }

        $doctor = Doctor::create($data);

        if ($request->filled('department_ids')) {
            $doctor->departments()->sync($request->department_ids);
        }

        return new DoctorResource($doctor);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:doctors,phone,' . $doctor->id,
            'national_code' => 'required|string|max:10|min:10|unique:doctors,national_code,' . $doctor->id,
            'card_number' => 'required|string|max:16|unique:doctors,card_number,' . $doctor->id,
            'medical_number' => 'required|string|max:16|unique:doctors,medical_number,' . $doctor->id,
            'birth_date' => 'required',
            'days' => 'string',
            'department_ids' => 'array',
            'department_ids.*' => 'exists:departments,id',
            'avatar' => 'nullable|image|max:2048',
            'resume' => 'nullable|file|mimetypes:application/pdf|max:2048',
        ], [
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'national_code.unique' => 'این کد ملی قبلا ثبت شده است.',
            'national_code.required' => 'فیلد کد ملی الزامی است.',
            'national_code.max' => 'کد ملی نمی تواند بیشتر از 10 کاراکتر باشد.',
            'national_code.min' => 'کد ملی نمی تواند کمتر از 10 کاراکتر باشد.',
            'card_number.unique' => 'این شماره کارت قبلا ثبت شده است.',
            'card_number.max' => 'شماره کارت نمی تواند بیشتر از 16 کاراکتر باشد.',
            'card_number.min' => 'شماره کارت نمی تواند کمتر از 16 کاراکتر باشد.',
            'medical_number.unique' => 'این شماره نظام روانشناسی قبلا ثبت شده است.',
            'medical_number.max' => 'شماره نظام روانشناسی نمی تواند بیشتر از 16 کاراکتر باشد.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $data = $validated;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('doctor_avatars', 'public');
        }

        if ($request->hasFile('resume')) {
            $data['resume'] = $request->file('resume')->store('doctor_resumes', 'public');
        }

        if ($request->filled('department_ids')) {
            $doctor->departments()->sync($request->department_ids);
        }

        $doctor->update($data);

        return new DoctorResource($doctor);
    }

    public function storePassword(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'password' => 'string|required|min:8'
        ]);

        if ($doctor->password && $doctor->password == $validated['password']) {
            return response()->json('رمز عبور جدید نمیتواند مشابه رمز عبور قبلی باشد', 400);
        }

        if (!$doctor->password || $doctor->password != $validated['password']) {
            $doctor->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return response()->json($doctor, 200);
    }


    public function destroy($id)
    {
        Doctor::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }

    public function getAllDoctorClients($id)
    {
        $doctor = Doctor::where('id', $id)->first();
        $clients = $doctor->clients->unique('id');
        $result = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name
            ];
        })->values();
        return response()->json($result, 200);
    }

    public function todaysClients($id)
    {
        $referrals = Referral::whereDate('date', Carbon::now())->get();
        $result = $referrals->map(function ($referral) use ($id) {
            $appUser = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('doctor_id', $id)
                ->first();

            if (!$appUser) {
                return;
            }

            $doctor = Doctor::find($appUser->doctor_id);
            $client = Client::find($appUser->client_id);

            $payment = Payment::where('referral_id', $referral->id)->first();

            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name ?? 'ناشناس',
                'client' => $client->name ?? 'ناشناس',
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status ?? 'نامشخص',
                'payment' => $referral->amount
            ];
        });

        $filteredResult = $result->filter()->values();
        return ReferralResource::collection($filteredResult);
    }

    public function yesterdaysClients($id)
    {
        $referrals = Referral::whereDate('date', Carbon::yesterDay())->get();
        $result = $referrals->map(function ($referral) use ($id) {
            $appUser = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('doctor_id', $id)
                ->first();

            if (!$appUser) {
                return;
            }

            $doctor = Doctor::find($appUser->doctor_id);
            $client = Client::find($appUser->client_id);

            $payment = Payment::where('referral_id', $referral->id)->first();

            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name ?? 'ناشناس',
                'client' => $client->name ?? 'ناشناس',
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status ?? 'نامشخص',
                'payment' => $referral->amount
            ];
        });

        $filteredResult = $result->filter()->values();
        return response()->json($filteredResult, 200);
    }

    public function tomorrowsClients($id)
    {
        $referrals = Referral::whereDate('date', Carbon::tomorrow())->get();
        $result = $referrals->map(function ($referral) use ($id) {
            $appUser = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('doctor_id', $id)
                ->first();

            if (!$appUser) {
                return;
            }

            $doctor = Doctor::find($appUser->doctor_id);
            $client = Client::find($appUser->client_id);

            $payment = Payment::where('referral_id', $referral->id)->first();

            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name ?? 'ناشناس',
                'client' => $client->name ?? 'ناشناس',
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status ?? 'نامشخص',
                'payment' => $referral->amount
            ];
        });

        $filteredResult = $result->filter()->values();
        return response()->json($filteredResult, 200);
    }

    public function lastSevenDaysClients($doctorId)
    {
        $referrals = Referral::query()
            ->whereBetween('date', [now()->subDays(7), now()])
            ->whereHas('doctor', function ($q) use ($doctorId) {
                $q->where('doctors.id', $doctorId);
            })
            ->with([
                'client',
                'doctor',
                'payment',
            ])
            ->orderBy('date')
            ->get();

        return ReferralResource::collection($referrals);
    }


    public function last30DaysClients($doctorId)
    {
        $referrals = Referral::query()
            ->whereBetween('date', [now()->subDays(30), now()])
            ->whereHas('doctor', function ($q) use ($doctorId) {
                $q->where('doctors.id', $doctorId);
            })
            ->with([
                'client',
                'doctor',
                'payment',
            ])
            ->orderBy('date')
            ->get();

        return ReferralResource::collection($referrals);
    }

    public function next30DaysClients($id)
    {
        $referrals = Referral::whereBetween('date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->orderBy('date')
            ->get();

        $result = $referrals->map(function ($referral) use ($id) {
            // بررسی وجود رکورد مرتبط در جدول referral_user
            $appUser = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('doctor_id', $id)
                ->first();

            if (!$appUser) {
                return; // اگر ارتباطی وجود نداشت، مقدار null بازگردانده می‌شود
            }

            // گرفتن اطلاعات دکتر و بیمار
            $doctor = Doctor::find($appUser->doctor_id);
            $client = Client::find($appUser->client_id);

            // گرفتن اطلاعات پرداخت
            $payment = Payment::where('referral_id', $referral->id)->first();

            // بازگرداندن اطلاعات معتبر
            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name ?? 'ناشناس',
                'client' => $client->name ?? 'ناشناس',
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status ?? 'نامشخص',
                'payment' => $referral->amount
            ];
        });

        // حذف مقادیر null و بازنشانی اندیس‌ها
        $filteredResult = $result->filter()->values();

        // بازگرداندن خروجی نهایی
        return response()->json($filteredResult, 200);
    }

    public function allClients($id)
    {
        $referrals = Referral::orderBy('date')->get();

        $result = $referrals->map(function ($referral) use ($id) {
            // بررسی وجود رکورد مرتبط در جدول referral_user
            $appUser = DB::table('referral_user')
                ->where('referral_id', $referral->id)
                ->where('doctor_id', $id)
                ->first();

            if (!$appUser) {
                return; // اگر ارتباطی وجود نداشت، مقدار null بازگردانده می‌شود
            }

            // گرفتن اطلاعات دکتر و بیمار
            $doctor = Doctor::find($appUser->doctor_id);
            $client = Client::find($appUser->client_id);

            // گرفتن اطلاعات پرداخت
            $payment = Payment::where('referral_id', $referral->id)->first();

            // بازگرداندن اطلاعات معتبر
            return [
                'referral_id' => $referral->id,
                'doctor' => $doctor->name ?? 'ناشناس',
                'client' => $client->name ?? 'ناشناس',
                'date' => $referral->date,
                'time' => $referral->time,
                'status' => $referral->status,
                'amount' => $referral->amount,
                'payment_status' => $payment->status ?? 'نامشخص',
                'payment' => $referral->amount
            ];
        });

        // حذف مقادیر null و بازنشانی اندیس‌ها
        $filteredResult = $result->filter()->values();

        // بازگرداندن خروجی نهایی
        return response()->json($filteredResult, 200);
    }

    public function sendTodaysSms($id)
    {

        $tomorrow = Carbon::tomorrow();

        $doctor = Doctor::where('id', $id)->first();
        $app_doc = DB::table('referral_user')->where('doctor_id', $id)->get();

        $apps = $app_doc->map(function ($app) {
            $today = Carbon::today();
            $appointment = Referral::whereDate('date', $today)->where('id', $app->referral_id)->first();
            return $appointment;
        });

        $filtered_apps = $apps->filter()->values();

        $text = "کلینیک ابراز\nنوبت های امروز شما:";
        $appointments = $filtered_apps->map(function ($app) {
            $referralUser = DB::table('referral_user')
                ->where('referral_id', $app->id)
                ->first();

            if ($referralUser) {
                $client = Client::find($referralUser->client_id);
                return [
                    'client_name' => $client ? $client->name : 'نامشخص',
                    'amount' => $app->amount,
                ];
            }

            return null;
        })->filter();

        foreach ($appointments as $appointment) {
            $text .= "\n{$appointment['client_name']} - {$appointment['amount']} تومان";
        }

        $data = [
            "lineNumber" => "9982005424",
            "messageText" => $text,
            "mobiles" => [$doctor->phone]
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

        return response()->json('success', 200);
    }

    public function sendTomorrowsSms($id)
    {
        $doctor = Doctor::where('id', $id)->first();
        $app_doc = DB::table('referral_user')->where('doctor_id', $id)->get();

        $apps = $app_doc->map(function ($app) {
            $tomorrow = Carbon::tomorrow();
            $appointment = Referral::whereDate('date', $tomorrow)->where('id', $app->referral_id)->first();
            return $appointment;
        });

        $filtered_apps = $apps->filter()->values();

        $text = "کلینیک ابراز\nنوبت های فردای شما:";
        $appointments = $filtered_apps->map(function ($app) {
            $referralUser = DB::table('referral_user')
                ->where('referral_id', $app->id)
                ->first();

            if ($referralUser) {
                $client = Client::find($referralUser->client_id);
                return [
                    'client_name' => $client ? $client->name : 'نامشخص',
                    'time' => $app->time,
                ];
            }

            return null;
        })->filter();

        foreach ($appointments as $appointment) {
            $text .= "\n{$appointment['client_name']} - ساعت {$appointment['time']}";
        }

        $data = [
            "lineNumber" => "30007487132891",
            "messageText" => $text,
            "mobiles" => [$doctor->phone]
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

        return response()->json('success', 200);
    }
}
