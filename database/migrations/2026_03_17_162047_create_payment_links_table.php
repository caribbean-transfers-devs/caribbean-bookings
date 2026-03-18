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
        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_id')->nullable();
            $table->string('link_code')->unique();
            $table->string('code');
            $table->string('email');
            $table->string('language');
            $table->string('type');
            $table->enum('currency', ['MXN', 'USD'])->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('link');
            $table->timestamps();
            
            $table->index('reservation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_links');
    }
};
