<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('gender', ['male', 'female'])->default('female');
            $table->enum('access_to_laptop', ['yes', 'no'])->default('yes');
            $table->string('current_education_level')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('github_link')->nullable();
            $table->string('cv_details')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreignId('track_id')->references('id')->on('tracks')->cascadeOnUpdate();
            $table->foreignId('course_id')->references('id')->on('courses')->cascadeOnDelete();
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
};
