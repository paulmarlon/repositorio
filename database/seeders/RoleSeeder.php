<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles del sistema académico
        Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Supervisor']);
        Role::firstOrCreate(['name' => 'Usuario']);
    }
}
