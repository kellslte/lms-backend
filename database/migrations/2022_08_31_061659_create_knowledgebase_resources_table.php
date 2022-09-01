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
        Schema::create('knowledgebase_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tag', ['students', 'facilitators', 'admins', 'mentors'])->default('students');
            $table->string('title');
            $table->string('moderator');
            $table->string('resource_link');
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
        Schema::dropIfExists('knowledgebase_resources');
    }
};
