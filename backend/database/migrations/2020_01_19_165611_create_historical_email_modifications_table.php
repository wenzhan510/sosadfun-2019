<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricalEmailModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_email_modifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token')->nullable()->index();
            $table->unsignedInteger('user_id')->default(0);
            $table->string('old_email')->nullable()->index();
            $table->string('new_email')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('old_email_verified_at')->nullable();
            $table->dateTime('email_changed_at')->nullable();
            $table->dateTime('admin_revoked_at')->nullable();
            $table->index(['user_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_email_modifications');
    }
}
