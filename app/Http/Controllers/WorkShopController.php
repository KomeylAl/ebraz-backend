<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\WorkshopResource;
use App\Models\WorkShop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WorkShopController extends Controller
{
    public function index(Request $request) {
        $query = Workshop::query();

        // جستجو روی عنوان یا توضیحات
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فیلتر بر اساس تاریخ شروع (اختیاری)
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', $request->query('start_date'));
        }

        // مرتب‌سازی
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $workshops = $query->paginate($perPage);

        // ریترن به فرمت Resource
        return WorkshopResource::collection($workshops);
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string',
            'excerpt'     => 'required|string',
            'content'     => 'required|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'week_day'    => 'nullable|string',
            'time'        => 'nullable|string',
            'organizers'  => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['img_path'] = $request->file('image')->store('workshop_images', 'public');
        }

        $workshop = WorkShop::create($data);

        return response()->json($workshop, 201);
    }

    public function show($id) {
        $workshop = WorkShop::with([
            'sessions',
            'participants' => function ($query) {
                $query->withPivot('approved', 'registered_at');
            }
        ])->findOrFail($id);
        return new WorkshopResource($workshop);
    }

    public function update(Request $request, $id) {
        $workshop = WorkShop::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string',
            'excerpt'     => 'required|string',
            'content'     => 'required|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'week_day'    => 'nullable|string',
            'time'        => 'nullable|string',
            'organizers'  => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['img_path'] = $request->file('image')->store('workshop_images', 'public');
        }

        $workshop->update($data);

        return response()->json($workshop, 200);
    }

    public function destroy($id) {
        $workshop = WorkShop::findOrFail($id);

        // اگر مسیر عکس موجود باشد، آن را حذف کن
        if ($workshop->img_path && Storage::disk('public')->exists($workshop->img_path)) {
            Storage::disk('public')->delete($workshop->img_path);
        }

        // حذف رکورد از دیتابیس
        $workshop->delete();

        return response()->json(['message' => 'کارگاه با موفقیت حذف شد.'], 200);
    }

}
