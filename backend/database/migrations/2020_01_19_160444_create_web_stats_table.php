<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('qiandaos')->default(0);
            $table->unsignedInteger('posts')->default(0);
            $table->unsignedInteger('chapters')->default(0);
            $table->unsignedInteger('reviews')->default(0);
            $table->unsignedInteger('new_users')->default(0);
            $table->unsignedBigInteger('daily_clicks')->default(0);
            $table->unsignedInteger('collections_bianyuan')->default(0);
            $table->unsignedInteger('collections_none_bianyuan')->default(0);
            $table->unsignedInteger('collections_recommended')->default(0);
            $table->unsignedInteger('users_with_none_bianyuan_3')->default(0);
            $table->unsignedInteger('users_with_recommended_3')->default(0);
            $table->unsignedInteger('daily_clicked_users')->default(0);
            $table->unsignedInteger('daily_clicks_median')->default(0);
            $table->unsignedInteger('daily_clicks_average')->default(0);
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_stats');
    }
}
