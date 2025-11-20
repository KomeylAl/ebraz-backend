<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

use App\Http\Resources\DepartmentResource;

class DepartmentController extends Controller
{
    public function index(Request $request) {
        $query = Department::query();

        // جستجو در عنوان یا خلاصه
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // مرتب‌سازی
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $departments = $query
            ->paginate($perPage)
            ->appends($request->query()); // برای حفظ پارامترهای صفحه‌بندی در لینک‌ها

        return DepartmentResource::collection($departments);
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:posts',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('department_images', 'public');
        }

        $department = Department::create($data);

        return new DepartmentResource($department);
    }

    public function show(Department $department) {
        return new DepartmentResource($department);
    }

    public function update(Request $request, Department $department) {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:posts,slug,' . $department->id,
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('department_images', 'public');
        }

        $department->update($data);

        return new DepartmentResource($department);
    }

    public function destroy(Department $department) {
        $department->delete();
        return response()->noContent();
    }
}
