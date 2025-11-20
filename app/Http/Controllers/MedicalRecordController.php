<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\Companion;
use Schema;

class MedicalRecordController extends Controller
{
    public function getClientRecord($id)
    {
        $record = MedicalRecord::where('client_id', $id)->first();
        return response()->json($record, 200);
    }

    public function store(Request $request, $client_id)
    {
        // ۱️⃣ بررسی وجود کلاینت
        $client = Client::findOrFail($client_id);

        // ۲️⃣ اعتبارسنجی داده‌ها
        $validated = $request->validate([
            'record_number' => 'required|string|unique:medical_records,record_number,' . optional($client->record)->id,
            'reference_source' => 'nullable|string',
            'admission_date' => 'required|date',
            'visit_date' => 'required|date',
            'doctor_id' => 'nullable|exists:doctors,id',
            'supervisor_id' => 'nullable|exists:doctors,id',
            'admin_id' => 'nullable|exists:admins,id',

            // برای تصاویر
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

            // برای همراه (اختیاری)
            'companion_name' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_address' => 'nullable|string|max:255',
        ]);

        // ۳️⃣ ساخت یا آپدیت پرونده
        $record = $client->record;

        if ($record) {
            $record->update([
                'admin_id'=> $validated['admin_id'],
            ]);
            $action = 'updated';
        } else {
            $record = $client->record()->create($validated);
            $action = 'created';
        }

        $nrecord = MedicalRecord::all();

        // // ۴️⃣ ذخیره تصاویر در جدول جداگانه (record_images_table)
        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $image) {
        //         $path = $image->store('medical_records/' . $client_id, 'public');

        //         // رکورد جدید در جدول تصاویر
        //         \DB::table('record_images_teble')->insert([
        //             'medical_record_id' => $record->id,
        //             'file_path' => $path,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // }

        // // ۵️⃣ ثبت یا به‌روزرسانی همراه (در جدول companions)
        // if ($request->filled('companion_name') && $request->filled('companion_phone')) {
        //     $companion = Companion::updateOrCreate(
        //         ['phone' => $request->companion_phone],
        //         [
        //             'name' => $request->companion_name,
        //             'address' => $request->companion_address,
        //         ]
        //     );

        //     // ارتباطش با رکورد (در صورت داشتن فیلد مخصوص یا جدول pivot)
        //     // مثلا اگه medical_records جدولش یه فیلد companion_id داره:
        //     if (Schema::hasColumn('medical_records', 'companion_id')) {
        //         $record->update(['companion_id' => $companion->id]);
        //     }
        // }

        // // ۶️⃣ پاسخ نهایی
        // return response()->json([
        //     'message' => "Record {$action} successfully",
        //     'record' => $record->load(['client']),
        // ], 201);

        return response()->json($record);
    }


}
