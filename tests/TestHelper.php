<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Helper para configurar el entorno de pruebas
 * 
 * Este archivo proporciona configuraciones adicionales para las pruebas,
 * incluyendo la configuración de la base de datos y otros aspectos
 * del entorno de testing.
 */
class TestHelper
{
    /**
     * Configura la base de datos para las pruebas
     * 
     * @return void
     */
    public static function setupDatabase()
    {
        // Configurar la base de datos SQLite en memoria para las pruebas
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);
    }

    /**
     * Ejecuta las migraciones necesarias para las pruebas
     * 
     * @return void
     */
    public static function runMigrations()
    {
        // Ejecutar las migraciones en la base de datos de pruebas
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--env' => 'testing']);
    }

    /**
     * Limpia la base de datos después de cada prueba
     * 
     * @return void
     */
    public static function cleanDatabase()
    {
        // Limpiar todas las tablas
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys=OFF;');
        \Illuminate\Support\Facades\DB::statement('DELETE FROM tickets;');
        \Illuminate\Support\Facades\DB::statement('DELETE FROM users;');
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys=ON;');
    }
}
