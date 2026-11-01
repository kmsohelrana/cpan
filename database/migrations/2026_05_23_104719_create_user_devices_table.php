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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('model_type');
            $table->string('device_id');
            $table->string('sim_id')->nullable();
            $table->text('additional')->nullable();
            $table->string('api_type');
            $table->string('token', 255);
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamp('token_generated_at')->useCurrent();
            $table->timestamp('token_expires_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
