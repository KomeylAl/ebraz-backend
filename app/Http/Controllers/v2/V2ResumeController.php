<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use Log;

class V2ResumeController extends Controller
{
    public function show()
    {
        $doctor = Auth()->user();
        $resume = Resume::where('doctor_id', $doctor->id)->first();
        return response()->json($resume);
    }

    public function store(Request $request)
    {
        // مرحله ۱: decode کردن فیلدهایی که JSON هستن
        $raw = $request->all();
        $doctor = Auth()->user();
        Log::info('Resume Log', $raw);

        foreach (['educations', 'experiences', 'skills', 'certifications', 'social_links'] as $field) {
            if (isset($raw[$field]) && is_string($raw[$field])) {
                $decoded = json_decode($raw[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $raw[$field] = $decoded;
                }
            }
        }

        // مرحله ۲: حالا validate امن انجام بده
        $data = validator($raw, [
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'educations' => 'nullable|array',
            'experiences' => 'nullable|array',
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'content' => 'nullable|string',
            'social_links' => 'nullable|array',
        ])->validate();

        // مرحله ۳: ذخیره یا آپدیت رزومه
        $resume = Resume::updateOrCreate(
            ['doctor_id' => $doctor->id],
            $data
        );

        return response()->json($resume);
    }
}
