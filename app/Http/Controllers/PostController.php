<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

use App\Http\Resources\PostResource;

class PostController extends Controller {
    public function index(Request $request) {
        $query = Post::query();

        // جستجو در عنوان یا خلاصه
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // فیلتر بر اساس دسته‌بندی
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // فیلتر بر اساس تگ‌ها (چندگانه)
        if ($request->filled('tag_ids')) {
            $tagIds = $request->query('tag_ids'); // باید array باشه: ?tag_ids[]=1&tag_ids[]=3
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        // فیلتر بر اساس وضعیت (اختیاری)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status')); // draft / published / archived
        }

        // مرتب‌سازی
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $posts = $query
            ->with(['author', 'category', 'tags'])
            ->withCount('comments')
            ->paginate($perPage)
            ->appends($request->query()); // برای حفظ پارامترهای صفحه‌بندی در لینک‌ها

        return PostResource::collection($posts);
    }


    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:posts',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'in:draft,published,archived|nullable',
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $validated['admin_id'] = auth()->id();

        $data = $validated;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts_images', 'public');
        }

        $post = Post::create($data);
        $post->tags()->sync($validated['tag_ids'] ?? []);

        return new PostResource($post->load(['author', 'category', 'tags']));
    }

    public function show(Post $post) {
        return new PostResource($post->load(['author', 'category', 'tags']));
    }

    public function update(Request $request, Post $post) {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $data = $validated;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts_images', 'public');
        }

        $post->update($data);
        if (isset($validated['tag_ids'])) {
            $post->tags()->sync($validated['tag_ids']);
        }

        return new PostResource($post->load(['author', 'category', 'tags']));
    }

    public function destroy(Post $post) {
        $post->delete();
        return response()->noContent();
    }
}

