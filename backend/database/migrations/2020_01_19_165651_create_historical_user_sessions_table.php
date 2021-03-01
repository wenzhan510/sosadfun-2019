<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricalUserSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_user_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->unsignedInteger('session_count')->default(0);
            $table->unsignedInteger('ip_count')->default(0);
            $table->unsignedInteger('ip_band_count')->default(0);
            $table->unsignedInteger('device_count')->default(0);
            $table->unsignedInteger('mobile_count')->default(0);
            $table->text('session_data')->nullable();
            $table->index(['user_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_user_sessions');
    }
}
