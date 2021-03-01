<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag_name', 10)->unique();//标签简称
            $table->string('tag_explanation')->nullable();//标签详解
            $table->string('tag_type', 10)->nullable()->index();//标签类别，原来的taginfo内容
            $table->boolean('is_bianyuan')->default(false);//是否边缘限制专有
            $table->boolean('is_primary')->default(false);//是否属于主要的tag（分两种情况，具有channellimit，和不具有channellimit-长短篇，科幻奇幻，）
            $table->unsignedInteger('channel_id')->default(0)->index();//是否某个channel专属
            $table->unsignedInteger('parent_id')->default(0)->index();//用于同人CP寻找同人原著，同人原著寻找同人作品其他分类
            $table->unsignedInteger('thread_count')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
