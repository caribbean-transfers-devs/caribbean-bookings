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
        if (!Schema::hasColumn('reservations', 'terminal')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->enum('terminal', ['T1', 'T2', 'T3', 'T4'])->nullable()->after('rate_group');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('terminal');
        });
    }
};
