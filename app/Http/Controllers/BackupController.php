<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Storage;

class BackupController extends Controller
{
    public function backupDoctors()
    {
        // 1. دریافت دیتا
        $doctors = Doctor::all();

        // 2. تبدیل به JSON
        $jsonData = $doctors->toJson(JSON_PRETTY_PRINT);

        // 3. تعیین مسیر ذخیره‌سازی
        $fileName = 'backups/doctors/doctors_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);

        $url = url(Storage::url($fileName));

        // 6. ذخیره رکورد بکاپ در دیتابیس (اختیاری ولی مهم)
        Backup::create([
            'type' => 'doctor',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        // 7. پاسخ به فرانت اند
        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }
}
