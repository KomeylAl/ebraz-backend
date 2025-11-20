<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('message')->nullable();

            $table->string('type')->default('system'); // system, reminder, appointment, message, etc.
            
            // مثلاً اگر نوتیف برای یک دکتر خاصه
            $table->nullableMorphs('notifiable'); // notifiable_type, notifiable_id

            // اولویت و کانال ارسال
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->json('delivery_channels')->nullable(); // مثلاً ["in_app","email","sms"]
            // وضعیت نوتیف
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');

            // برای متادیتا یا لینک‌ها و داده‌ی اضافه
            $table->json('meta')->nullable();

            // زمان زمان‌بندی ارسال (در آینده برای queue مفیده)
            $table->timestamp('scheduled_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
