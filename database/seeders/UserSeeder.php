<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $users = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'María García',
                'email' => 'maria.garcia@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Luis Martínez',
                'email' => 'luis.martinez@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
