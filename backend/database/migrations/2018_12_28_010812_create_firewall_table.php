<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirewallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firewall', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_address', 45)->index();//被封禁IP地址
            $table->unsignedInteger('user_id')->default(0)->index();//执行封禁的管理员id
            $table->string('reason')->nullable();//封禁理由
            $table->dateTime('created_at')->nullable();//创建时间
            $table->dateTime('end_at')->nullable();//停止封禁时间
            $table->boolean('is_valid')->default(true);//是否可用
            $table->boolean('is_public')->default(true);//对外公示
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
         Schema::dropIfExists('firewall');
     }
}
