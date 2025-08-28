<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Ticket para gestionar tickets de soporte
 * 
 * Este modelo representa un ticket de soporte en el sistema.
 * Cada ticket está asociado a un usuario y tiene un estado que puede ser:
 * - open: Abierto (ticket recién creado)
 * - in_progress: En progreso (ticket siendo atendido)
 * - closed: Cerrado (ticket resuelto)
 * 
 * El modelo incluye:
 * - Relación con el modelo User
 * - Scopes para filtrar por estado
 * - Accessor para obtener el estado en español
 * - Campos fillable para asignación masiva
 * 
 * @package App\Models
 */
class Ticket extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',    // ID del usuario asignado al ticket
        'title',      // Título del ticket
        'description', // Descripción detallada del problema
        'status',     // Estado actual del ticket (open, in_progress, closed)
    ];

    /**
     * Relación con el modelo User
     * 
     * Cada ticket pertenece a un usuario específico.
     * Esta relación permite acceder fácilmente a la información
     * del usuario asignado al ticket.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar tickets por estado específico
     * 
     * Permite filtrar tickets por cualquier estado válido:
     * - 'open': Tickets abiertos
     * - 'in_progress': Tickets en progreso
     * - 'closed': Tickets cerrados
     * 
     * Uso: Ticket::byStatus('open')->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status Estado por el cual filtrar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para obtener solo tickets abiertos
     * 
     * Filtra automáticamente tickets con estado 'open'.
     * Útil para mostrar tickets que requieren atención.
     * 
     * Uso: Ticket::open()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope para obtener solo tickets en progreso
     * 
     * Filtra automáticamente tickets con estado 'in_progress'.
     * Útil para mostrar tickets que están siendo atendidos.
     * 
     * Uso: Ticket::inProgress()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope para obtener solo tickets cerrados
     * 
     * Filtra automáticamente tickets con estado 'closed'.
     * Útil para mostrar tickets ya resueltos.
     * 
     * Uso: Ticket::closed()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Accessor para obtener el estado del ticket en español
     * 
     * Convierte los valores de estado de la base de datos a texto legible en español.
     * Este accessor se puede usar automáticamente en las vistas.
     * 
     * Estados disponibles:
     * - 'open' → 'Abierto'
     * - 'in_progress' → 'En Progreso'
     * - 'closed' → 'Cerrado'
     * 
     * Uso en vistas: {{ $ticket->status_text }}
     * 
     * @return string Estado del ticket en español
     */
    public function getStatusTextAttribute()
    {
        // Mapeo de estados de la base de datos a texto en español
        $statuses = [
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'closed' => 'Cerrado'
        ];

        // Retorna el texto en español o el valor original si no existe mapeo
        return $statuses[$this->status] ?? $this->status;
    }
}
