<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Resources\TagResource;

class TagController extends Controller {
    public function index(Request $request) {
        $query = Tag::query();

        // جستجو روی نام یا توضیح
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
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
        return TagResource::collection($categories);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|unique:tags',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog_tag_images', 'public');
        }

        $tag = Tag::create($data);
        return new TagResource($tag);
    }

    public function show(Tag $tag) {
        return new TagResource($tag);
    }

    public function update(Request $request, Tag $tag) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'slug' => 'sometimes|required|string|unique:tags,slug,' . $tag->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog_tag_images', 'public');
        }

        $tag->update($data);
        return new TagResource($tag);
    }

    public function destroy(Tag $tag) {
        $tag->delete();
        return response()->noContent();
    }
}
