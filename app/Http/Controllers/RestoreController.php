<?php

namespace App\Http\Controllers;

use App\Models\Restore;
use App\Models\Doctor;
use App\Models\Resume;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Workshop;
use App\Models\About;
use Illuminate\Http\Request;
use Storage;

class RestoreController extends Controller
{
    public function restoreAdmins()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Admin::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'admin']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restoreDoctors(Request $request)
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Doctor::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'doctor']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }


    public function restoreDoctorResumes()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Resume::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'doctorResume']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }
    
    public function restoreClients()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Client::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'client']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restorePosts()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Post::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'post']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restoreCategories()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Category::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'category']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restoreTags()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Tag::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'tag']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restoreWorkshops()
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            Workshop::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'workshop']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }

    public function restoreAbout(Request $request)
    {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'فرمت داده معتبر نیست.'
            ], 400);
        }

        foreach ($data['data'] as $item) {

            if (!is_array($item)) continue;

            // حذف فیلدهای تاریخ
            unset($item['created_at']);
            unset($item['updated_at']);

            // حذف https:// از logo_path چون دیتابیس مسیر نسبی ذخیره می‌کند
            if (!empty($item['logo_path']) && str_starts_with($item['logo_path'], 'http')) {
                $item['logo_path'] = str_replace(url('storage') . '/', '', $item['logo_path']);
            }

            // آپدیت یا ساخت رکورد
            About::updateOrCreate(
                ['id' => $item['id']], 
                $item
            );
        }

        Restore::create(['type' => 'about']);

        return response()->json([
            'status' => 'success',
            'message' => 'اطلاعات با موفقیت بازیابی شدند.'
        ]);
    }


}
