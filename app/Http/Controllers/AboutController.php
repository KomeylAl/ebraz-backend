<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\About;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function index() {
        $info = About::first();

        if (!$info) {
            return response()->json([
                'message' => 'اطلاعات پیدا نشد'
            ], 404);
        }

        return response()->json($info, 200);
    }

    public function upsert(Request $request) {
        // اعتبارسنجی
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'about'         => 'required|string',
            'address'       => 'required|string',
            'phones'        => 'required|string',
            'mobile_phones' => 'required|string',
            'image'         => 'nullable|image|max:5120'
        ]);
    
        // رکورد اول
        $info = About::first();
    
        // داده های فرم
        $data = [
            'title'            => $validated['title'],
            'about'            => $validated['about'],
            'address'          => $validated['address'],
            'phones'           => $validated['phones'],
            'mobile_phones'    => $validated['mobile_phones'],
            'lat'              => $request->lat ?? "1",
            'long'             => $request->long ?? "1",
        ];
    
        // آپلود تصویر
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('pictures', 'public');
            $data['logo_path'] = $path;
        }
    
        // ذخیره یا آپدیت
        if ($info) {
            $info->update($data);
        } else {
            $info = About::create($data);
        }
    
        return response()->json($info, 200);
    }
    
}
