<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE reservations_items 
            MODIFY op_one_status_operation 
            ENUM('PENDING','E','C','NOSHOW','CANCELLED','OK') 
            NULL DEFAULT 'PENDING'
        ");
        DB::statement("
            ALTER TABLE reservations_items 
            MODIFY op_two_status_operation 
            ENUM('PENDING','E','C','NOSHOW','CANCELLED','OK') 
            NULL DEFAULT 'PENDING'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE reservations_items 
            MODIFY op_one_status_operation 
            ENUM('PENDING','E','C','CANCELLED','OK') 
            NULL DEFAULT 'PENDING'
        ");
        DB::statement("
            ALTER TABLE reservations_items 
            MODIFY op_two_status_operation 
            ENUM('PENDING','E','C','CANCELLED','OK') 
            NULL DEFAULT 'PENDING'
        ");
    }
};