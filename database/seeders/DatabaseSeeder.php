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
            'nombres' => 'paul',
            'paterno' => 'quispe',
            'materno' => 'veizaga',
            'ci' => '12345678',
            'email' => 'paul@adhara.tech',
            'password' => Hash::make('7539518520'),
            'avatar' => null, // Opcional, al estar en null usará las iniciales
            'activo' => true,
        ]);
    }
}
