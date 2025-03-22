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
            $table->time('check_in_time')->index();
            $table->time('check_out_time')->index();
            $table->time('end_check_out_time')->nullable()->index();
            $table->time('extra_hours')->nullable()->index();
            $table->unsignedBigInteger('vehicle_id')->nullable()->index();
            $table->unsignedBigInteger('driver_id')->nullable()->index();
            $table->enum('status', ['A', 'F', 'DT'])->nullable(); //
            $table->time('check_in_time_fleetio')->nullable()->index();
            $table->time('check_out_time_fleetio')->nullable()->index();
            $table->text('observations')->nullable();
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
