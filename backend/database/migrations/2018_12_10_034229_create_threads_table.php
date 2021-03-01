<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();//作者id
            $table->unsignedInteger('channel_id')->default(0)->index();//是哪个频道下面的

            $table->string('title', 30)->nullable()->index();//讨论帖总标题
            $table->string('brief', 50)->nullable();//讨论帖简介小灰字
            $table->text('body')->nullable();//讨论帖正文
            $table->boolean('is_anonymous')->default(false);//是否匿名
            $table->string('majia', 10)->nullable();//作者马甲
            $table->string('creation_ip', 45)->nullable();//创建时IP地址
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('edited_at')->nullable();//最后编辑时间

            $table->boolean('is_locked')->default(false);//是否锁帖
            $table->boolean('is_public')->default(true);//是否公开
            $table->boolean('is_bianyuan')->default(false);//是否边缘限制
            $table->boolean('no_reply')->default(false);//是否禁止回复
            $table->boolean('no_reward')->default(false);//是否不给奖励

            $table->boolean('use_markdown')->default(false);//是否使用md语法
            $table->boolean('use_indentation')->default(true);//是否使用段首缩进格式
            $table->boolean('recommended')->default(false);//是否被编推
            $table->integer('salt')->default(0);//盐粒数量
            $table->integer('fish')->default(0);//咸鱼数量
            $table->integer('ham')->default(0);//火腿数量

            $table->integer('view_count')->default(0);//点击数
            $table->integer('reply_count')->default(0);//得到的回复数
            $table->integer('collection_count')->default(0);//被收藏次数
            $table->integer('download_count')->default(0);//被下载次数
            $table->integer('jifen')->default(0);//总积分
            $table->integer('weighted_jifen')->default(0);//被字数模块平衡后的积分
            $table->integer('total_char')->default(0);//components总字数

            $table->dateTime('responded_at')->nullable();//最后被回应时间
            $table->unsignedInteger('last_post_id')->default(0);//最后回帖是谁
            $table->dateTime('add_component_at')->nullable();//最后新增物品时间
            $table->unsignedInteger('last_component_id')->default(0);//最新物品
            $table->unsignedInteger('first_component_id')->default(0);//最新物品

            $table->dateTime('deleted_at')->nullable();// 软删除必备
            $table->dateTime('deletion_applied_at')->nullable();//申请延迟删除时间

            $table->index(['is_public','is_bianyuan','channel_id']);
            $table->index(['is_public','channel_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
