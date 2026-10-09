<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nombres' => 'Admin',
            'paterno' => 'Sistema',
            'materno' => '',
            'ci' => '12345678',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'avatar' => null, // Opcional, al estar en null usará las iniciales
            'activo' => true,
        ]);
    }
}
