<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_infos', function (Blueprint $table) {
            $table->unsignedInteger('post_id')->primary();
            $table->integer('order_by')->default(0);//排序号码
            $table->string('abstract')->nullable();//内容引文
            $table->text('warning')->nullable();//文前预警
            $table->text('annotation')->nullable();//章节备注
            $table->unsignedInteger('previous_id')->default(0);//前一章的章节序数（post_id）
            $table->unsignedInteger('next_id')->default(0);//后一章的章节序数（post_id）
            $table->unsignedInteger('reviewee_id')->default(0)->index();//所推荐（或涉及）的内容的ID
            $table->string('reviewee_type',10)->nullable();//所推荐（或涉及）的内容的类型
            $table->tinyInteger('rating')->default(0);//推荐星级
            $table->unsignedInteger('redirect_count')->default(0);//安利成功次数
            $table->tinyInteger('author_attitude')->default(0);//作者态度
            $table->string('summary', 10)->nullable()->index();//举报结果
            $table->index(['reviewee_id','reviewee_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_infos');
    }
}
