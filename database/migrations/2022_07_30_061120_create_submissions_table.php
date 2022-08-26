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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('submittable_id');
            $table->string('submittable_type');
            $table->uuid('taskable_id');
            $table->string('taskable_type');
            $table->enum('status', ['submitted', 'graded'])->default('submitted');
            $table->unsignedBigInteger('grade')->default(0);
            $table->string('link_to_resource');
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
        Schema::dropIfExists('submissions');
    }
};
