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
        Schema::create('driver_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->time('check-in_time')->index();
            $table->time('check-out_time')->index();
            $table->unsignedBigInteger('vehicle_id')->nullable()->index();
            $table->unsignedBigInteger('driver_id')->nullable()->index();
            $table->enum('status', ['A', 'F', 'DT'])->nullable(); //
            $table->time('check-in_time_fleetio')->nullable()->index();
            $table->time('check-out_time_fleetio')->nullable()->index();
            $table->text('observations');
            $table->timestamps();
            $table->softDeletes(); // Agrega la columna deleted_at para SoftDeletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_schedules');
    }
};
