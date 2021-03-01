<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homeworks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 30)->nullable()->index();//作业名称，如“第几次作业”
            $table->string('topic', 30)->nullable()->index();//作业主题，如“瓶中信”
            $table->integer('level')->default(0)->index();
            $table->integer('ham_base')->default(0);//作业奖励的火腿基础
            $table->boolean('is_active')->default(true);//是否仍是进行中的作业
            $table->boolean('allow_watch')->default(true);
            $table->dateTime('registration_on')->nullable();
            $table->integer('worker_registration_limit')->default(0);
            $table->integer('critic_registration_limit')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('registration_thread_id')->default(0)->index();
            $table->unsignedInteger('profile_thread_id')->default(0);
            $table->unsignedInteger('summary_thread_id')->default(0);
            $table->unsignedInteger('purchase_count')->default(0);
            $table->unsignedInteger('worker_count')->default(0);
            $table->unsignedInteger('critic_count')->default(0);
            $table->unsignedInteger('finished_work_count')->default(0);
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
        Schema::dropIfExists('homeworks');
    }
}
