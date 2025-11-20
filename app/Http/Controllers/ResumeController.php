<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function show($doctorId)
    {
        $resume = Resume::where('doctor_id', $doctorId)->first();
        return response()->json($resume);
    }

    public function store(Request $request, $doctorId)
    {
        // مرحله ۱: decode کردن فیلدهایی که JSON هستن
        $raw = $request->all();

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
            'social_links' => 'nullable|array',
        ])->validate();

        // مرحله ۳: ذخیره یا آپدیت رزومه
        $resume = Resume::updateOrCreate(
            ['doctor_id' => $doctorId],
            $data
        );

        return response()->json($resume);
    }

}
