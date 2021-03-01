<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('poster_id')->index();//发送人id
            $table->unsignedInteger('receiver_id')->index();//接收人id
            $table->unsignedInteger('body_id')->default(0);//消息内容id
            $table->boolean('private')->default(false);//是否私人对话
            $table->boolean('seen')->default(false);//是否已读
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('deleted_at')->nullable();//创建时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
