<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique(); // 用户名
            $table->string('email')->unique(); // 邮箱
            // $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->index(); // 密码
            $table->boolean('activated')->default(0);// 是否激活
            $table->rememberToken()->nullable()->index();
            $table->dateTime('created_at'); // 注册时间
            $table->dateTime('deleted_at')->nullable();// 软删除必备
            $table->integer('level')->default(0)->index(); // 用户等级
            $table->integer('title_id')->default(0); // 头衔ID
            $table->string('role', 10)->nullable(); // 站内身份
            $table->integer('quiz_level')->default(0); // 用户答题等级
            $table->boolean('no_logging')->default(0); // 是否禁止登陆
            $table->boolean('no_posting')->default(0); // 是否禁止登陆
            $table->boolean('no_ads')->default(0); // 是否免广告
            $table->boolean('no_homework')->default(0); // 是否免作业
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
