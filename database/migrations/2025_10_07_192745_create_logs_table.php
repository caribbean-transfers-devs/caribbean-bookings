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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reservation_id')->unsigned()->nullable();
            $table->string('process_id', 32)->nullable()->comment('Opcional: Se utiliza por si quieres asignarle un id a tu proceso y trackear todos los logs relacionados');
            $table->enum('type', ['info', 'warning', 'error'])->default('info');
            $table->string('category')->nullable();
            $table->longText('message');
            $table->longText('exception');
            $table->timestamps();

            $table->index('reservation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
