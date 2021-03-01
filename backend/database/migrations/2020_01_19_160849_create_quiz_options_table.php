<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quiz_id')->default(0)->index();
            $table->string('body')->nullable();
            $table->string('explanation')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('edited_at')->nullable();
            $table->unsignedInteger('select_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_options');
    }
}
