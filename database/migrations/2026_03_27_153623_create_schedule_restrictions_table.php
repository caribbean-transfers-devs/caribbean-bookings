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
        Schema::create('schedule_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nombre descriptivo de la restricción');
            $table->tinyInteger('is_active')->default(1)->comment('1 = activa, 0 = inactiva');
            $table->datetime('start_at')->comment('Inicio de la restricción (ej. 2026-12-31 22:00)');
            $table->datetime('end_at')->comment('Fin de la restricción (ej. 2027-01-01 08:30)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_restrictions');
    }
};
