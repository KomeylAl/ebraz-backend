<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use Log;

class NotificationController extends Controller
{
    // 📄 نمایش همه نوتیف‌ها (مثلاً برای ادمین)
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

    public function unreadNotifications(Request $request)
    {
        $user = auth()->user();
        $userType = get_class($user);

        // نوتیف‌هایی که مخصوص کاربر یا عمومی برای اون نوع کاربر هستن
        $query = Notification::with('notifiable')
            ->where(function ($q) use ($user, $userType) {
                $q->where(function ($sub) use ($user, $userType) {
                    // نوتیف‌های مخصوص کاربر
                    $sub->where('notifiable_type', $userType)
                        ->where('notifiable_id', $user->id);
                })
                    ->orWhere(function ($sub) use ($userType) {
                        // نوتیف‌های عمومی مخصوص اون نوع کاربر
                        $sub->where('notifiable_type', $userType)
                            ->whereNull('notifiable_id');
                    })
                    ->orWhereNull('notifiable_type'); // نوتیف‌های عمومی برای همه
            })
            // 🔥 نوتیف‌هایی که هنوز کاربر نخونده
            ->whereDoesntHave('reads', function ($q) use ($user, $userType) {
                $q->where('receiver_type', $userType)
                    ->where('receiver_id', $user->id);
            });

        // 🧠 جستجو (اختیاری)
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // 🎚️ فیلترها (type، priority، تاریخ و ...)
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->query('from_date'),
                $request->query('to_date')
            ]);
        }

        // 📑 مرتب‌سازی و صفحه‌بندی
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $perPage = (int) $request->query('per_page', 20);

        $notifications = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);

        return response()->json($notifications);
    }


    // 📨 ایجاد نوتیف جدید
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string',
            'type' => 'string|nullable',
            'delivery_channel' => 'string|nullable',
            'notifiable_type' => 'nullable|string',
            'notifiable_id' => 'nullable|integer',
            'meta' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
        ]);


        $notification = Notification::create($data);

        switch ($notification->priority) {
            case 'high':
                $notification->update(['delivery_channels' => ['in_app', 'email', 'sms']]);
                break;
            case 'medium':
                $notification->update(['delivery_channels' => ['in_app', 'email']]);
                break;
            default:
                $notification->update(['delivery_channels' => ['in_app']]);
                break;
        }
        // در آینده با Event جایگزین می‌شه
        event(new \App\Events\NotificationCreated($notification));
        return response()->json($notification, 201);
    }

    // 🔔 نوتیف‌های یک کاربر خاص (admin / doctor / client)
    public function userNotifications(Request $request)
    {
        $user = $request->user(); // بسته به نوع گارد، مثلاً auth('doctor') یا auth('client')
        return Notification::where(function ($q) use ($user) {
            $q->where('notifiable_type', get_class($user))
                ->where('notifiable_id', $user->id);
        })
            ->orWhereNull('notifiable_id') // نوتیف عمومی
            ->with('reads')
            ->latest()
            ->get();
    }

    // ✅ مارک کردن نوتیف به عنوان خوانده‌شده
    public function markAsRead(Notification $notification, Request $request)
    {
        $user = $request->user();
        $userType = get_class($user);

        // بررسی اینکه این نوتیف مربوط به خود کاربر یا عمومی برای نقش اوست
        $isForUser =
            ($notification->notifiable_type === $userType && $notification->notifiable_id === $user->id)
            || ($notification->notifiable_type === $userType && $notification->notifiable_id === null);

        if (!$isForUser) {
            return response()->json(['message' => 'Unauthorized to mark this notification'], 403);
        }

        NotificationRead::updateOrCreate(
            [
                'notification_id' => $notification->id,
                'receiver_type' => $userType,
                'receiver_id' => $user->id,
            ],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Marked as read']);
    }


    public function test(Request $request)
    {
        Log::info('🚀 test endpoint called');

        // شبیه‌سازی ارسال نوتیف برای Doctor با id=1
        $notification = Notification::create([
            'title' => 'مصرف منایع',
            'message' => 'منابع سیستم به شدت درگیر شده اند. با مدیر سیستم تماس بگیرید.',
            'type' => 'system',
            'priority' => 'high',
            'notifiable_type' => \App\Models\Admin::class,
            'notifiable_id' => null,
        ]);

        // ست کردن کانال بر اساس priority
        switch ($notification->priority) {
            case 'high':
                $notification->update(['delivery_channels' => ['in_app', 'email', 'sms']]);
                break;
            case 'medium':
                $notification->update(['delivery_channels' => ['in_app', 'email']]);
                break;
            default:
                $notification->update(['delivery_channels' => ['in_app']]);
                break;
        }

        Log::info('✅ Notification created', [
            'id' => $notification->id,
            'title' => $notification->title,
            'channel' => "Admin.{$notification->notifiable_id}"
        ]);

        // fire event - این event به کانال Doctor.1 broadcast می‌شه
        event(new NotificationCreated($notification));

        return response()->json([
            'message' => 'Test notification sent!',
            'data' => $notification,
            'broadcast_channel' => "Admin.{$notification->notifiable_id}",
        ]);
    }

}
