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
        Schema::create('meetings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('caption');
            $table->string('host_name');
            $table->string('link');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date');
            $table->enum('type', ['sotu', 'onboarding', 'class']);
            $table->string('calendarId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
};
