<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricalUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_user_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->unsignedInteger('daily_clicks')->default(0);
            $table->unsignedInteger('daily_posts')->default(0);
            $table->unsignedInteger('daily_chapters')->default(0);
            $table->unsignedInteger('daily_characters')->default(0);
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
        Schema::dropIfExists('historical_user_datas');
    }
}
