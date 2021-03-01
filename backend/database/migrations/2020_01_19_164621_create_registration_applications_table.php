<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable()->index();
            $table->boolean('has_quizzed')->default(false);
            $table->boolean('is_passed')->default(false);
            $table->boolean('is_forbidden')->default(false);
            $table->unsignedInteger('reviewer_id')->default(0)->index();
            $table->boolean('cut_in_line')->default(false);
            $table->string('token', 40)->nullable()->index();
            $table->unsignedInteger('user_id')->default(0)->index();
            $table->string('quiz_questions')->nullable();
            $table->unsignedInteger('essay_question_id')->default(0);
            $table->text('body')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('ip_address_last_quiz', 45)->nullable()->index();
            $table->string('ip_address_verify_email', 45)->nullable()->index();
            $table->string('ip_address_submit_essay', 45)->nullable()->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('last_invited_at')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('send_verification_at')->nullable();
            $table->string('email_token', 10)->nullable();
            $table->unsignedInteger('submission_count')->default(0);
            $table->unsignedInteger('quiz_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_applications');
    }
}
