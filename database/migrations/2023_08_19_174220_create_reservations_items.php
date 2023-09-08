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
        Schema::create('reservations_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->string('code')->unique();

            //Reservation data
            $table->unsignedBigInteger('destination_service_id')->nullable();
            $table->string('from_name');
            $table->string('from_lat');
            $table->string('from_lng');        
            $table->unsignedBigInteger('from_zone')->nullable();

            $table->string('to_name');
            $table->string('to_lat');
            $table->string('to_lng');
            $table->unsignedBigInteger('to_zone')->nullable();

            $table->integer('distance_time'); //Seconds
            $table->string('distance_km');
            $table->tinyInteger('is_round_trip')->default(0);

            $table->string('flight_number')->nullable();
            $table->text('flight_data')->nullable();
            $table->integer('passengers');

            $table->enum('op_one_status', ['PENDING', 'COMPLETED', 'NOSHOW', 'CANCELLED'])->default('PENDING');            
            $table->dateTime('op_one_pickup')->nullable();
            $table->enum('op_two_status', ['PENDING', 'COMPLETED', 'NOSHOW', 'CANCELLED'])->default('PENDING');
            $table->dateTime('op_two_pickup')->nullable();
                        
            $table->index('destination_service_id');
            $table->index('from_zone');
            $table->index('to_zone');



            $table->index('reservation_id');
            $table->timestamps();            
            //$table->foreign('reservation_id')->references('id')->on('reservations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations_items');
    }
};
