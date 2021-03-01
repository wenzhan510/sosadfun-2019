<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->default(0)->index();
            $table->unsignedInteger('receiver_id')->default(0)->index();
            $table->unsignedInteger('votable_id')->default(0);
            $table->string('votable_type',10)->nullable();
            $table->string('attitude_type', 10)->nullable();  //upvote,downvote,funnyvote,foldvote
            $table->dateTime('created_at')->nullable()->index();
            $table->index(['votable_id', 'votable_type']);
            $table->unique(['user_id', 'votable_id', 'votable_type', 'attitude_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
