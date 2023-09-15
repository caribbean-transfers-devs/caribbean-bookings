<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PanelSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Roles
        DB::table('roles')->insert([
            'role' => "Administrador",
        ]);
        DB::table('roles')->insert([
            'role' => "Contabilidad",
        ]);
        DB::table('roles')->insert([
            'role' => "Gerente - Call Center",
        ]);
        DB::table('roles')->insert([
            'role' => "Agente - Call Center",
        ]);

        //Módulos
        DB::table('modules')->insert([
            'module' => "Usuarios",
        ]);
        DB::table('modules')->insert([
            'module' => "Roles",
        ]);
        DB::table('modules')->insert([
            'module' => "Reservaciones",
        ]);
        DB::table('modules')->insert([
            'module' => "TPV",
        ]);
        DB::table('modules')->insert([
            'module' => "General",
        ]);

        //Submodulos
        // - Usuarios
        DB::table('submodules')->insert([
            'module_id' => 1,
            'submodule' => "Ver usuarios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 1,
            'submodule' => "Crear usuarios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 1,
            'submodule' => "Editar usuarios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 1,
            'submodule' => "Activar/Desacrivar usuarios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 1,
            'submodule' => "Agregar IP's sin restricción",
        ]);
        // - Roles
        DB::table('submodules')->insert([
            'module_id' => 2,
            'submodule' => "Ver roles",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 2,
            'submodule' => "Crear roles",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 2,
            'submodule' => "Editar roles",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 2,
            'submodule' => "Eliminar roles",
        ]);
        // - Reservaciones
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Ver reservaciones",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Editar datos personales",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Agregar servicios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Editar servicios",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Agregar pagos",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Editar pagos",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Eliminar pagos",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Agregar ventas",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Editar ventas",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Eliminar ventas",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Re-enviar voucher al cliente",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Enviar SMS/Whatsapp",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Enviar Invitación de pago",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Agregar Seguimientos",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Cancelar reservacion",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 3,
            'submodule' => "Ver historial",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 4,
            'submodule' => "Ver TPV",
        ]);
        DB::table('submodules')->insert([
            'module_id' => 5,
            'submodule' => "Buscador de reservaciones",
        ]);

        //Roles - Permisos
        // -> ADMIN
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 1,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 2,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 3,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 4,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 5,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 6,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 7,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 8,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 9,
        ]);
        DB::table('roles_permits')->insert([
            'role_id' => 1,
            'submodule_id' => 10,
        ]);

        // Usuarios
        // -> Usuario principal
        DB::table('users')->insert([
            'name' => "Cristobal",
            'email' => "demo@demo.com",
            'status' => 1,
            'restricted' => 0,
            'email_verified_at' => NULL,
            'password' => '$2a$12$UGri/MetelMO75mCDeOg9OqmxwkCy/HehJZPFJjwUbI3d4YyiXJCK',
            'remember_token' => NULL,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => NULL,
        ]);
        // Usuario -> ROL
        //Usuario principal
        DB::table('user_roles')->insert([
            'role_id' => 1,
            'user_id' => 1,
        ]);
    }
}