<?php

namespace App\Services;

class SmsService
{
    public static function send($phone, $message)
    {
        // اینجا می‌تونی در آینده هر API مثل Kavenegar یا Ghasedak یا sms.ir بذاری
        \Log::info("SMS to {$phone}: {$message}");
        return true;
    }
}
