<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() == 0) {
            $this->command->error('No hay usuarios disponibles. Ejecute primero UserSeeder.');
            return;
        }

        $tickets = [
            [
                'user_id' => $users->random()->id,
                'title' => 'Problema con el acceso al sistema',
                'description' => 'No puedo acceder al sistema desde esta mañana. Me aparece un error de autenticación.',
                'status' => 'open',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Solicitud de nueva funcionalidad',
                'description' => 'Necesitamos agregar una nueva funcionalidad para exportar reportes en formato PDF.',
                'status' => 'in_progress',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Error en la base de datos',
                'description' => 'Se está produciendo un error al guardar los datos en la base de datos.',
                'status' => 'closed',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Problema de rendimiento',
                'description' => 'La aplicación está muy lenta al cargar las páginas principales.',
                'status' => 'open',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Actualización de perfil de usuario',
                'description' => 'Necesito actualizar mi información de perfil pero no puedo guardar los cambios.',
                'status' => 'in_progress',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Problema con notificaciones',
                'description' => 'No estoy recibiendo las notificaciones por email cuando se actualiza mi ticket.',
                'status' => 'closed',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Solicitud de capacitación',
                'description' => 'Necesito capacitación sobre las nuevas funcionalidades del sistema.',
                'status' => 'open',
            ],
            [
                'user_id' => $users->random()->id,
                'title' => 'Error en el reporte mensual',
                'description' => 'El reporte mensual no está mostrando los datos correctos.',
                'status' => 'in_progress',
            ],
        ];

        foreach ($tickets as $ticketData) {
            Ticket::create($ticketData);
        }
    }
}
