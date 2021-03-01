<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');//post_id
            $table->string('type',10)->nullable();//'chapter', 'question', 'answer', 'request', 'post', 'comment', 'review', 'poll'...
            $table->unsignedInteger('user_id')->default(0)->index();//作者id
            $table->unsignedInteger('thread_id')->default(0)->index();//讨论帖id

            $table->string('title', 30)->nullable();//标题
            $table->string('brief', 50)->nullable();//节选
            $table->text('body')->nullable();//回帖文本本身
            $table->boolean('is_anonymous')->default(false);//是否匿名回帖
            $table->string('majia', 10)->nullable();//作者马甲
            $table->string('creation_ip', 45)->nullable();//创建时IP地址
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('edited_at')->nullable();//最后编辑时间

            $table->unsignedInteger('in_component_id')->default(0)->index();//从属单元id
            $table->unsignedInteger('reply_to_id')->default(0);//如果是回帖，给出它回复对象的id
            $table->string('reply_to_brief')->nullable();//如果是回帖，给出它回复对象的brief
            $table->integer('reply_to_position')->default(0);//回复对象句子在原来评论中的位置
            $table->unsignedInteger('last_reply_id')->default(0)->index();//最新回复id
            $table->boolean('is_bianyuan')->default(false);//是否属于边缘内容（以至于需要对非注册用户隐藏内容）
            $table->boolean('use_markdown')->default(false);//是否使用md语法
            $table->boolean('use_indentation')->default(true);//是否使用段首缩进格式

            $table->integer('upvote_count')->default(0);//赞
            $table->integer('downvote_count')->default(0);//踩
            $table->integer('funnyvote_count')->default(0);//搞笑
            $table->integer('foldvote_count')->default(0);//折叠

            $table->integer('reply_count')->default(0);//得到的回复数
            $table->integer('view_count')->default(0);//得到的单独点击数
            $table->integer('char_count')->default(0);//总字数

            $table->dateTime('responded_at')->nullable();//最后被回应时间
            $table->dateTime('deleted_at')->nullable();// 软删除必备

            $table->tinyInteger('fold_state')->default(0);//折叠

            $table->tinyInteger('len')->default(0);//长度
            $table->boolean('is_comment')->default(0);//是否点评
            $table->index(['thread_id','fold_state','is_comment','created_at']);
            $table->index(['thread_id','user_id']);
            $table->index(['reply_to_id','reply_to_position']);
            $table->index(['type','is_bianyuan','len']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
