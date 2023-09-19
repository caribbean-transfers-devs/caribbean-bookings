<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ApiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {         
        
        //API
        DB::table('sites')->insert([
            'name' => "caribbean-transfers.com",
            'logo' => "https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/logo.png",
            'payment_domain' => "https://caribbean-transfers.com",
            'color' => "#CE8506",
            'transactional_email' => "bookings@caribbean-transfers.com",
            'transactional_email_send' => 1,
            'is_commissionable' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sites')->insert([
            'name' => "[CS] caribbean-transfers.com",
            'logo' => "https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/logo.png",
            'payment_domain' => "https://caribbean-transfers.com",
            'color' => "#CE8506",
            'transactional_email' => "bookings@caribbean-transfers.com",
            'transactional_email_send' => 1,
            'is_commissionable' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        #Tipos de ventas
        DB::table('sales_types')->insert([
            'name' => "Transportación",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('sales_types')->insert([
            'name' => "Descuento",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        #Destinos
        DB::table('destinations')->insert([
            'name' => 'Cancun',
            'status' => 1,
            'cut_off' => 12,
            'time_zone' => 'America/Cancun',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('providers')->insert([
            'name' => 'Cancun Provider',
            'transactional_phone' => '(998) 171-0512',
            'transactional_emails' => 'development@caribbean-transfers.com, otrujillo.dev@gmail.com',
            'is_default' => 1,
            'destination_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        #Zonas
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Cancun Airport",
            'is_primary' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Cancun",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Puerto Juarez",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Costa Mujeres",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Puerto Morelos",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Playa del Carmen",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Playa Car",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Puerto Aventuras",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Akumal",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Tulum Pueblo",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Tulum Zona Hotelera",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('zones')->insert([
            'destination_id' => 1,
            'name' => "Bahía Principe",
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        #Servicios
        DB::table('destination_services')->insert([
            'name' => 'Taxi',
            'passengers' => 3,
            'luggage' => 4,
            'order' => 1,
            'destination_id' => 1,
            'status' => 1,
            'image_url' => 'https://ik.imagekit.io/zqiqdytbq/transportation-api/taxi.jpg',
            'price_type' => 'vehicle',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('destination_services')->insert([
            'name' => 'Private service',
            'passengers' => 8,
            'luggage' => 5,
            'order' => 2,
            'destination_id' => 1,
            'status' => 1,
            'image_url' => 'https://ik.imagekit.io/zqiqdytbq/transportation-api/standard.jpg',
            'price_type' => 'passenger',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        #Servicios - Traducciones
        DB::table('destination_services_translate')->insert([
            'lang' => 'es',
            'translation' => "Taxi ES",
            'destination_services_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        #Servicios - Traducciones
        DB::table('destination_services_translate')->insert([
            'lang' => 'es',
            'translation' => "Servicio privado",
            'destination_services_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //Rate Groups
        DB::table('rates_groups')->insert([
            'name' => "DEFAULT",
            'code' => "xLjDl18",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('rates_groups')->insert([
            'name' => "Afiliados",
            'code' => "vPFGoWD",
            'created_at' => now(),
            'updated_at' => now(),
        ]);         


        //Tipo de Cambio
        DB::table('exchange_rate')->insert([
            'origin' => "USD",
            'destination' => "USD",
            'exchange_rate' => 1,
            'operation' => "multiplication",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('exchange_rate')->insert([
            'origin' => "USD",
            'destination' => "MXN",
            'exchange_rate' => 18.00,
            'operation' => "division",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payments_exchange_rate')->insert([
            'origin' => "MXN",
            'destination' => "MXN",
            'exchange_rate' => 1,
            'operation' => "multiplication",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('payments_exchange_rate')->insert([
            'origin' => "USD",
            'destination' => "MXN",
            'exchange_rate' => 18,
            'operation' => "multiplication",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('payments_exchange_rate')->insert([
            'origin' => "USD",
            'destination' => "USD",
            'exchange_rate' => 1,
            'operation' => "multiplication",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('payments_exchange_rate')->insert([
            'origin' => "MXN",
            'destination' => "USD",
            'exchange_rate' => 18,
            'operation' => "division",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //API
        DB::table('api')->insert([
            'user' => "api",
            'secret' => "1234567890",
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}
