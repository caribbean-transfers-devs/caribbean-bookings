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
        if (Schema::hasTable('terminal_payments_exchange_rate'))
            return;
        
        Schema::create('terminal_payments_exchange_rate', function (Blueprint $table) {
            $table->id();

            $table->string('origin');
            $table->string('destination');
            $table->decimal('exchange_rate', 10, 2);
            $table->enum('operation', ['multiplication', 'division'])->default('multiplication');
            $table->enum('terminal', ['T1', 'T2', 'T3', 'T4'])->default('T1');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_payments_exchange_rate');
    }
};
