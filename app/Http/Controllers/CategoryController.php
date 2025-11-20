<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller {
    public function index(Request $request) {
        $query = Category::query();

        // جستجو روی نام یا توضیح
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // مرتب‌سازی
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $categories = $query->paginate($perPage);

        // بازگشت در قالب ریسورس
        return CategoryResource::collection($categories);
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|unique:categories',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog_category_images', 'public');
        }

        $category = Category::create($data);
        return new CategoryResource($category);
    }

    public function show(Category $category) {
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'slug' => 'sometimes|required|string|unique:categories,slug,' . $category->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog_category_images', 'public');
        }

        $category->update($data);
        return new CategoryResource($category);
    }

    public function destroy(Category $category) {
        $category->delete();
        return response()->noContent();
    }
}
