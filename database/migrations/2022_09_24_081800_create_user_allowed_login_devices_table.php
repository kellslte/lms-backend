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
        Schema::create('user_allowed_login_devices', function (Blueprint $table) {
            $table->id();
            $table->uuid("loggable_id");
            $table->string("loggable_type");
            $table->json("device_specification");
            // $table->ipAddress("ip_address");
            // $table->string('device_type');
            // $table->string('browser_version');
            // $table->string('browser_name');
            // $table->string('device_platform');
            // $table->string('platform_version');
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
        Schema::dropIfExists('user_allowed_login_devices');
    }
};
