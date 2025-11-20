<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workshop_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_shop_id')->constrained()->onDelete('cascade');
            $table->date('session_date')->nullable();
            $table->time('start_time')->nullable();  // مثلا 14:00
            $table->time('end_time')->nullable();    // مثلا 16:00
            $table->string('location')->nullable();  // یا لینک جلسه آنلاین
            $table->string('link')->nullable();  // یا لینک جلسه آنلاین
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
        Schema::dropIfExists('workshop_sessions');
    }
};
