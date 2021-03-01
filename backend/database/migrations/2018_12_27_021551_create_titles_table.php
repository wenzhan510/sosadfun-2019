<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30)->nullable()->index();//头衔名称
            $table->text('description')->nullable();//头衔解释
            $table->unsignedInteger('user_count')->default(0);//多少人获得了这个头衔
            $table->unsignedInteger('style_id')->default(0);//头衔样式id
            $table->string('type',10)->nullable();//头衔样式字串
            $table->unsignedInteger('level')->default(0);//等级
            $table->string('style_type',10)->nullable();//头衔样式字串
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('titles');
    }
}
