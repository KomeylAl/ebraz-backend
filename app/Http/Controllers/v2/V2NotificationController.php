<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class V2NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userType = get_class($user);

        $query = Notification::with('notifiable')
            ->where(function ($q) use ($user, $userType) {
                $q->where(function ($sub) use ($user, $userType) {
                    // نوتیف‌های مخصوص این کاربر
                    $sub->where('notifiable_type', $userType)
                        ->where('notifiable_id', $user->id);
                })
                    ->orWhere(function ($sub) use ($userType) {
                        // نوتیف‌های عمومی برای همه از همین نوع
                        $sub->where('notifiable_type', $userType)
                            ->whereNull('notifiable_id');
                    })
                    ->orWhereNull('notifiable_type'); // نوتیف‌های عمومی برای همه
            });

        // 🔍 جستجو بر اساس عنوان یا پیام
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // 🔎 فیلتر بر اساس نوع نوتیف (اختیاری)
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        // ⚙️ فیلتر بر اساس سطح اهمیت (priority)
        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        // 📅 فیلتر بر اساس بازه زمانی
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->query('from_date'),
                $request->query('to_date')
            ]);
        }

        // 🔢 مرتب‌سازی (defaults: created_at DESC)
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // 📄 صفحه‌بندی
        $perPage = (int) $request->query('per_page', 20);
        $notifications = $query->paginate($perPage);

        return response()->json($notifications);
    }
}
