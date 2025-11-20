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
    public function up() {
        Schema::table('participant_workshop', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('work_shop_id');
            $table->timestamp('joined_at')->nullable()->after('approved');
        });

        Schema::table('participant_workshop', function (Blueprint $table) {
            $table->unique(['participant_id', 'work_shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
