<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('body')->nullable();//题头文字本身
            $table->unsignedInteger('user_id')->index();//提交的人是谁
            $table->boolean('is_anonymous')->default(false);//是否匿名
            $table->string('majia',10)->nullable();//马甲名称
            $table->boolean('notsad')->default(false);//是否并非丧题头
            $table->boolean('approved')->default(false);//是否已经在用
            $table->boolean('reviewed')->default(false);//是否已经审核过
            $table->unsignedInteger('reviewer_id')->default(0);//审核人是谁
            $table->unsignedInteger('fish')->default(0);//所获得咸鱼数目
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('deleted_at')->nullable();//删除时间
            $table->tinyInteger('review_count')->default(0);//前审核次数
            $table->tinyInteger('pass_count')->default(0);//通过的前审核次数
            $table->index(['approved','created_at']);
            $table->index(['approved','notsad']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
