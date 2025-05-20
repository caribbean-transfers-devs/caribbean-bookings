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
        Schema::create('reservations_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->index(); // Asegurar que es unsigned
            $table->text('message_refund');            
            $table->enum('status', ['REFUND_REQUESTED', 'REFUND_MADE', 'REFUND_COMPLETED'])->default('REFUND_REQUESTED'); //
            $table->timestamp('end_at');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations_refunds');
    }
};
