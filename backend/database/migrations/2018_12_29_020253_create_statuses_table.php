<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('brief')->nullable();
            $table->text('body')->nullable();
            $table->string('attachable_type', 10)->nullable();//附件类型：status(转发当条状态), chapter, book, thread, post..., picture
            $table->unsignedInteger('attachable_id')->default(0);//附件id
            $table->unsignedInteger('reply_to_id')->default(0)->index();//回复的status是哪个
            $table->unsignedInteger('last_reply_id')->default(0)->index();//最后回复的status是哪个
            $table->string('creation_ip', 45);//创建的ip

            $table->boolean('no_reply')->default(false);//禁止跟帖
            $table->boolean('is_public')->default(true);//是否公开可见
            $table->unsignedInteger('reply_count')->default(0);//回复数
            $table->unsignedInteger('forward_count')->default(0);//转发数
            $table->unsignedInteger('upvote_count')->default(0);//赞数
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('deleted_at')->nullable();//创建时间
            $table->index(['attachable_id','attachable_type']);
            $table->index(['reply_to_id','is_public','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
