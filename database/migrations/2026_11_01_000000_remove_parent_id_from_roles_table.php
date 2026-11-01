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
        if (Schema::hasColumn('roles', 'parent_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('roles', 'parent_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('guard_name');
            });
        }
    }
};

