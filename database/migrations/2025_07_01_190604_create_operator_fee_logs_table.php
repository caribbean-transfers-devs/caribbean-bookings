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
        Schema::create('operator_fee_logs', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('operator_fee_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('operator_fee_id')->index(); // Asegurar que es unsigned
            // $table->foreign('operator_fee_id')->references('id')->on('operator_fees')->onDelete('cascade'); // Relación con usuarios

            // $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('user_id')->index(); // Asegurar que es unsigned
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Relación con usuarios            

            $table->string('action'); // create, update, delete
            $table->json('old_data')->nullable(); // Datos anteriores (para updates)
            $table->json('new_data')->nullable(); // Datos nuevos
            $table->text('notes')->nullable(); // Comentarios adicionales            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_fee_logs');
    }
};
