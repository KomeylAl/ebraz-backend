<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');

            $table->string('title')->nullable(); // عنوان کلی رزومه مثلاً "روان‌درمانگر شناختی-رفتاری"
            $table->text('bio')->nullable(); // توضیحات کلی درباره پزشک
            $table->string('specialization')->nullable(); // تخصص اصلی

            $table->json('educations')->nullable(); // مثلاً [{"degree":"کارشناسی ارشد روانشناسی","institution":"دانشگاه تهران","year":"2018"}]
            $table->json('experiences')->nullable(); // [{"role":"روانشناس بالینی","organization":"کلینیک مهر","from":"2018","to":"2023"}]
            $table->json('skills')->nullable(); // ["CBT","ACT","زوج‌درمانی"]
            $table->json('certifications')->nullable(); // ["گواهینامه مشاوره خانواده","CBT Advanced Training"]
            $table->json('social_links')->nullable(); // {"linkedin":"...","instagram":"..."}
            $table->string('file_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
