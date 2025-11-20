<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_shops', function (Blueprint $table) {
            // افزودن ستون‌ها
            $table->string('slug')->nullable()->after("title");
            $table->text('excerpt')->nullable()->after("slug");

            // حذف ستون description
            $table->dropColumn('description');
        });
    }

    public function down()
    {
        Schema::table('work_shops', function (Blueprint $table) {
            // برگرداندن ستون description
            $table->string('description')->nullable();

            // حذف ستون‌های جدید
            $table->dropColumn(['slug', 'excerpt']);
        });
    }
};
