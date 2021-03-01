<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->index();
            $table->string('donation_email')->nullable()->index();
            $table->dateTime('donated_at')->nullable();
            $table->unsignedInteger('donation_amount')->nullable()->index();
            $table->boolean('show_amount')->default(true);
            $table->boolean('is_anonymous')->default(true);
            $table->string('donation_majia', 50)->nullable();
            $table->text('donation_message')->nullable();
            $table->string('donation_kind', 10)->nullable();
            $table->boolean('is_claimed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_records');
    }
}
