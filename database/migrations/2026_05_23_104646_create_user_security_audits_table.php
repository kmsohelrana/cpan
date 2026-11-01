<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_security_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('model_type');
            $table->string('device_id')->nullable();
            $table->string('sim_id')->nullable();
            $table->string('imei')->nullable();
            $table->string('google_ad_id')->nullable();
            $table->string('ip_address', 20)->nullable();
            $table->string('api_name', 225);
            $table->string('api_type');
            $table->string('user_agent')->nullable();
            $table->string('browser_name', 100)->nullable();
            $table->string('browser_version', 100)->nullable();
            $table->string('platform', 10)->nullable();
            $table->string('screen_size', 50)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lon', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_security_audits');
    }
};
