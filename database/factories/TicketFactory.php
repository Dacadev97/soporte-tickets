<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar datos de prueba del modelo Ticket
 * 
 * Este factory permite crear instancias de Ticket con datos aleatorios
 * para usar en pruebas y seeders. Genera datos realistas para:
 * - Títulos de tickets de soporte
 * - Descripciones detalladas de problemas
 * - Estados válidos del sistema
 * - Asociación con usuarios existentes
 */
class TicketFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente al factory
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define el estado por defecto del modelo
     *
     * @return array
     */
    public function definition()
    {
        // Estados válidos para los tickets
        $statuses = ['open', 'in_progress', 'closed'];
        
        // Títulos comunes de tickets de soporte
        $titles = [
            'Error en el sistema de login',
            'Problema con la carga de archivos',
            'Error 500 en la página principal',
            'No se pueden enviar emails',
            'Problema con la base de datos',
            'Error en el formulario de contacto',
            'Problema con la autenticación',
            'Error al procesar pagos',
            'Problema con la interfaz de usuario',
            'Error en la generación de reportes'
        ];

        // Descripciones detalladas de problemas
        $descriptions = [
            'El sistema presenta errores intermitentes que afectan la funcionalidad principal.',
            'Los usuarios reportan problemas al intentar acceder a ciertas secciones del sistema.',
            'Se ha detectado un error crítico que impide el funcionamiento normal de la aplicación.',
            'El rendimiento del sistema se ha degradado significativamente en las últimas horas.',
            'Los usuarios no pueden completar ciertas tareas debido a errores en la interfaz.',
            'Se ha identificado un problema de seguridad que requiere atención inmediata.',
            'El sistema no responde correctamente a las solicitudes de los usuarios.',
            'Se han reportado múltiples errores que afectan la experiencia del usuario.',
            'El problema persiste a pesar de los intentos de resolución implementados.',
            'Se requiere una investigación más profunda para identificar la causa raíz del problema.'
        ];

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->randomElement($descriptions),
            'status' => $this->faker->randomElement($statuses),
        ];
    }

    /**
     * Estado para tickets abiertos
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open',
            ];
        });
    }

    /**
     * Estado para tickets en progreso
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
            ];
        });
    }

    /**
     * Estado para tickets cerrados
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'closed',
            ];
        });
    }

    /**
     * Estado para tickets con título específico
     *
     * @param string $title
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTitle($title)
    {
        return $this->state(function (array $attributes) use ($title) {
            return [
                'title' => $title,
            ];
        });
    }

    /**
     * Estado para tickets con descripción específica
     *
     * @param string $description
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withDescription($description)
    {
        return $this->state(function (array $attributes) use ($description) {
            return [
                'description' => $description,
            ];
        });
    }
}
