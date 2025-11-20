<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use App\Services\SmsService; // در ادامه می‌سازیم

class SendNotificationChannels implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NotificationCreated $event)
    {
        $notification = $event->notification;
        $channels = $notification->delivery_channels ?? ['in_app'];

        // ۱️⃣ ارسال درون اپ (in_app) → همین ذخیره در دیتابیسه
        // ۲️⃣ ایمیل
        if (in_array('email', $channels)) {
            if ($notification->notifiable && method_exists($notification->notifiable, 'email')) {
                Mail::to($notification->notifiable->email)
                    ->send(new GenericNotificationMail($notification));
            }
        }

        // ۳️⃣ پیامک
        if (in_array('sms', $channels)) {
            if ($notification->notifiable && method_exists($notification->notifiable, 'phone')) {
                SmsService::send(
                    $notification->notifiable->phone,
                    $notification->message ?? $notification->title
                );
            }
        }

        // وضعیت
        $notification->update(['status' => 'sent']);
    }
}
