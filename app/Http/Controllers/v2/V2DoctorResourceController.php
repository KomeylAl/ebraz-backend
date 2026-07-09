<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorResource;
use App\Http\Resources\DoctorResorceResorce;

class V2DoctorResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorResource::all();
        

        // جستجو
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // مرتب سازی
        $sortBy = $request->query('sort_by', 'created_at'); // پیشفرض بر اساس created_at
        $sortDirection = $request->query('sort_direction', 'desc'); // پیشفرض نزولی
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $resources = $query->paginate($perPage);

        // ریترن به فرمت Resource
        return DoctorResorceResorce::collection($resources);
    }
}
