<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();//是谁收藏的
            $table->unsignedInteger('thread_id')->index();//收藏的是哪个thread
            $table->boolean('keep_updated')->default(true);//是否发送更新提示
            $table->boolean('updated')->default(false);//是否存在新消息/更新的提示
            $table->unsignedInteger('group_id')->default(0)->index();//从属的收藏页
            $table->unsignedInteger('last_read_post_id')->default(0);//最后阅读的postid
            $table->unique(['user_id','thread_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
}
