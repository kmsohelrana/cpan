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
        Schema::create('health_metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('component', 20)->comment('application|server|database|storage|queue|redis');
            $table->string('status', 20)->comment('healthy|warning|critical|unknown');
            $table->double('value')->nullable()->comment('Headline number: storage %, db ms, queue pending count, etc.');
            $table->string('message', 255);
            $table->json('payload')->comment('Full detail array for this component');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['component', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_metric_snapshots');
    }
};
