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
        Schema::create('operator_fees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la zona o grupo de zonas
            $table->decimal('base_amount', 10, 2); // Importe base
            $table->decimal('commission_percentage', 5, 2); // % de comisión
            $table->longtext('zone_ids'); // IDs de zonas agrupadas
            $table->timestamps();
            $table->softDeletes(); // Agrega la columna deleted_at para SoftDeletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_fees');
    }
};
