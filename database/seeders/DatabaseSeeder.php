<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GestionAcademica;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Llamamos al RoleSeeder para crear los roles
        $this->call([
            RoleSeeder::class,
        ]);

        // 2. Obtenemos o aseguramos la gestión académica activa (ID 1 por defecto al hacer fresh)
        $gestionActiva = GestionAcademica::firstOrCreate(
            ['activo' => true],
            ['anio' => 2026, 'periodo' => 'Primer Periodo']
        );

        // 3. Creamos al usuario administrador
        $user = User::create([
            'nombres' => 'paul',
            'paterno' => 'quispe',
            'materno' => 'veizaga',
            'ci' => '4751005',
            'email' => 'paul@adhara.tech',
            'password' => Hash::make('7539518520'),
            'avatar' => null,
            'activo' => true,
        ]);

        // 4. Asignar el rol nativo de Spatie para validaciones globales (hasRole)
        $user->assignRole('Administrador');

        // 5. Asignamos el rol de Administrador en la tabla pivote 'model_has_role_gestions' para la gestión activa
        $rolAdmin = Role::where('name', 'Administrador')->first();

        if ($rolAdmin && $gestionActiva) {
            DB::table('model_has_role_gestions')->insert([
                'role_id' => $rolAdmin->id,
                'model_type' => User::class,
                'model_id' => $user->id,
                'gestion_id' => $gestionActiva->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
