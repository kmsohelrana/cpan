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
        Schema::create('user_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_id');
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->unsignedBigInteger('action_by')->nullable();
            $table->string('employee_id', 50)->nullable();
            $table->string('name');
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->enum('language', ['en', 'bn'])->default('en');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_audits');
    }
};
