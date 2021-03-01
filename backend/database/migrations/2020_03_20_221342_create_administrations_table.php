<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();//who did the administration
            $table->string('task')->nullable;
            $table->text('reason')->nullable;
            $table->string('record')->nullable;
            $table->dateTime('created_at')->nullable()->index();
            $table->dateTime('deleted_at')->nullable();// 软删除必备
            $table->integer('administratee_id')->unsigned()->default(0)->index();//被处理用户id
            $table->string('administratable_type', 10)->nullable();//被处理内容id
            $table->integer('administratable_id')->unsigned()->default(0);//被处理内容id
            $table->boolean('is_public')->default(1);//是否公开处理
            $table->string('summary', 10)->nullable();//处理性质
            $table->integer('report_post_id')->unsigned()->default(0)->index();//这个处理对应的举报贴ID
            $table->index(['administratee_id','is_public','created_at']);
            $table->index(['administratable_id','administratable_type']);
            $table->index(['is_public','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrations');
    }
}
