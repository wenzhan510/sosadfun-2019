<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeworkInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 40)->nullable()->index();
            $table->unsignedInteger('homework_id')->default(0);
            $table->unsignedInteger('user_id')->default(0)->index();
            $table->unsignedInteger('level')->default(0);
            $table->string('role', 10)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_redeemed')->defalt(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homework_invitations');
    }
}
