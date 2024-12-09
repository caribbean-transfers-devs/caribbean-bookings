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
        Schema::create('exchange_rate_reports', function (Blueprint $table) {
            $table->id();
            $table->double('exchange', 255, 2); // 8 dígitos en total, 2 de ellos después del punto decimal
            $table->date('date_init'); // YYYY-MM-DD
            $table->date('date_end'); // YYYY-MM-DD
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rate_reports');
    }
};
