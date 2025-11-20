<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Doctor;
use App\Models\Referral;
use PDF;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index() {
        $invoices = Invoice::orderBy('id', 'desc')->get();
        $result = $invoices->map(function ($invoice) {
            $client = Client::where('id', $invoice->client_id)->first();
            return [
                'id' => $invoice->id,
                'client' => $client->name,
                'from_date' => $invoice->from_date,
                'to_date' => $invoice->to_date,
                'file_path' => $invoice->file_path
            ];
        });
        return response()->json($result, 200);
    }

    public function store(Request $request) {

        $client = Client::where('id', $request->client)->first();
        $apps_user = DB::table('referral_user')->where('client_id', $request->client)->get();
        $result = $apps_user->map(function ($app) use ($request) {
            $apps = Referral::where('id', $app->referral_id)
                ->where('date', '>=', $request->from_date)
                ->where('date', '<=', $request->to_date)
                ->first();
            $doctor = Doctor::where('id', $app->doctor_id)->first();
            $carbonDate = Carbon::createFromFormat('Y-m-d', $apps->date);
            $persianDate = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');
            return [
                'id' => $app->id,
                'doctor' => $doctor->name,
                'date' => $apps->date,
                'amount' => $apps->amount
            ];
        });

        $total = 0;
        for ($i = 0; $i < count($result); $i++) {
            $total += $result[$i]['amount'];
        }

        $today = Carbon::now();
        $jalali_today = Jalalian::fromCarbon($today)->format('Y/m/d');

        $fileName = 'invoice_' . time() . '_' . $client->name . '.pdf';
        $filePath = storage_path('app/public/pdf/' . $fileName);

        $pdf = \PDF::loadView('pdf', ['data' => $result, 'client' => $client, 'date' => $jalali_today, 'total' => $total])
            ->setPaper('a4')
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10);

        file_put_contents($filePath, $pdf->output());

        if (!Storage::exists('public/pdf')) {
            Storage::makeDirectory('public/pdf');
        }

        $fileUrl = asset('storage/pdf/' . $fileName);

        $invoice = Invoice::create([
            'client_id' => $request->client,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'admin_id' => $request->admin,
            'file_path' => $fileUrl
        ]);

        return response()->json($invoice, 200);
    }

    public function show(Request $request) {

        $client = Client::where('id', 1)->first();
        $apps_user = DB::table('referral_user')->where('client_id', 1)->get();
        $result = $apps_user->map(function ($app) {
            $apps = Referral::where('id', $app->referral_id)->first();
            $doctor = Doctor::where('id', $app->doctor_id)->first();
            $carbonDate = Carbon::createFromFormat('Y-m-d', $apps->date);
            $persianDate = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');
            return [
                'id' => $app->id,
                'doctor' => $doctor->name,
                'date' => $persianDate,
                'amount' => $apps->amount
            ];
        });

        $total = 0;
        for ($i = 0; $i < count($result); $i++) {
            $total += $result[$i]['amount'];
        }

        $today = Carbon::now();
        $jalali_today = Jalalian::fromCarbon($today)->format('Y/m/d');
        // $pdf = Pdf::loadView('pdf', ['data' => $result]);

        $fileName = 'invoice_' . time() . '_' . $client->name . '.pdf';
        $filePath = storage_path('app/public/pdf/' . $fileName);

        $pdf = \PDF::loadView('pdf', ['data' => $result, 'client' => $client, 'date' => $jalali_today, 'total' => $total])
            ->setPaper('a4')
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10);

        file_put_contents($filePath, $pdf->output());

        if (!Storage::exists('public/pdf')) {
            Storage::makeDirectory('public/pdf');
        }
        
        // لینک عمومی برای فایل
        $fileUrl = asset('storage/pdf/' . $fileName);
        //   return view('pdf', ['data' => $result, 'client' => $client, 'date' => $jalali_today, 'total' => $total]);
        // return $pdf->download('invoice.pdf');
        return response()->json($fileUrl, 200);
        
    }

    public function getReferrals(Request $request)
    {

        $clientId = $request->client;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // دریافت مراجعات با فیلتر تاریخ
        $referrals = DB::table('referral_user')
            ->join('referrals', 'referral_user.referral_id', '=', 'referrals.id')
            ->join('doctors', 'referral_user.doctor_id', '=', 'doctors.id')
            ->where('referral_user.client_id', $clientId)
            ->whereBetween('referrals.date', [$fromDate, $toDate])
            ->orderBy('referrals.date', 'asc')
            ->select(
                'referrals.id as referral_id',
                'referrals.date',
                'referrals.amount',
                'doctors.name as doctor_name'
            )
            ->get();

        // تبدیل تاریخ‌ها به شمسی
        $result = $referrals->map(function ($referral) {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $referral->date);
            $persianDate = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');

            return [
                'id' => $referral->referral_id,
                'doctor' => $referral->doctor_name,
                'date' => $persianDate,
                'amount' => $referral->amount,
            ];
        });

        $total = 0;
        for ($i = 0; $i < count($result); $i++) {
            $total += $result[$i]['amount'];
        }

        $today = Carbon::now();
        $jalali_today = Jalalian::fromCarbon($today)->format('Y/m/d');

        $client = Client::where('id', $request->client)->first();
        $fileName = 'invoice_' . time() . '_' . $client->name . '.pdf';
        $filePath = storage_path('app/public/pdf/' . $fileName);

        $pdf = \PDF::loadView('pdf', ['data' => $result, 'client' => $client, 'date' => $jalali_today, 'total' => $total])
            ->setPaper('a4')
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10);

        file_put_contents($filePath, $pdf->output());

        if (!Storage::exists('public/pdf')) {
            Storage::makeDirectory('public/pdf');
        }

        $fileUrl = asset('storage/pdf/' . $fileName);

        $invoice = Invoice::create([
            'client_id' => $request->client,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'admin_id' => $request->admin,
            'file_path' => $fileUrl
        ]);

        return response()->json($result, 201);
    }
}
