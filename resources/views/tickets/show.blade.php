{{-- 
    Vista: Detalles del Ticket (Show)
    =================================
    
    Esta vista muestra todos los detalles de un ticket específico.
    Incluye:
    - Información completa del ticket (título, descripción, estado)
    - Datos del usuario asignado
    - Fechas de creación y actualización
    - Panel de acciones (editar, eliminar, volver)
    - Historial de cambios del ticket
    - Diseño responsive con sidebar
    
    Variables disponibles:
    - $ticket: Modelo Ticket con relación de usuario cargada
--}}

@extends('layouts.app')

@section('title', 'Detalles del Ticket #' . $ticket->id)

@section('content')
{{-- Contenedor principal con layout de dos columnas --}}
<div class="row">
    {{-- Columna principal con información del ticket --}}
    <div class="col-md-8">
        {{-- Tarjeta principal con detalles del ticket --}}
        <div class="card">
            {{-- Encabezado con título y botones de acción --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Ticket #{{ $ticket->id }}
                    </h4>
                    <div>
                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
            {{-- Cuerpo de la tarjeta con contenido del ticket --}}
            <div class="card-body">
                {{-- Encabezado con título, fechas y estado --}}
                <div class="row mb-4">
                    {{-- Información principal del ticket --}}
                    <div class="col-md-8">
                        <h5 class="card-title">{{ $ticket->title }}</h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            Creado el {{ $ticket->created_at->format('d/m/Y \a \l\a\s H:i') }}
                        </p>
                        @if($ticket->updated_at != $ticket->created_at)
                            <p class="text-muted">
                                <i class="fas fa-edit me-1"></i>
                                Última actualización: {{ $ticket->updated_at->format('d/m/Y \a \l\a\s H:i') }}
                            </p>
                        @endif
                    </div>
                    {{-- Badge de estado del ticket --}}
                    <div class="col-md-4 text-end">
                        @if($ticket->status == 'open')
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>{{ $ticket->status_text }}
                            </span>
                        @elseif($ticket->status == 'in_progress')
                            <span class="badge bg-primary fs-6">
                                <i class="fas fa-spinner me-1"></i>{{ $ticket->status_text }}
                            </span>
                        @else
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check me-1"></i>{{ $ticket->status_text }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Sección de descripción del ticket --}}
                <div class="mb-4">
                    <h6 class="fw-bold">
                        <i class="fas fa-align-left me-2"></i>Descripción
                    </h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>
                </div>

                {{-- Información adicional en dos columnas --}}
                <div class="row">
                    {{-- Información del usuario asignado --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold">
                            <i class="fas fa-user me-2"></i>Información del Usuario
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1">
                                    <strong>Nombre:</strong> {{ $ticket->user->name ?? 'N/A' }}
                                </p>
                                <p class="mb-1">
                                    <strong>Email:</strong> {{ $ticket->user->email ?? 'N/A' }}
                                </p>
                                <p class="mb-0">
                                    <strong>ID Usuario:</strong> {{ $ticket->user_id }}
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- Información técnica del ticket --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold">
                            <i class="fas fa-info-circle me-2"></i>Información del Ticket
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1">
                                    <strong>ID:</strong> #{{ $ticket->id }}
                                </p>
                                <p class="mb-1">
                                    <strong>Estado:</strong> {{ $ticket->status_text }}
                                </p>
                                <p class="mb-0">
                                    <strong>Días desde creación:</strong> 
                                    {{ $ticket->created_at->diffInDays(now()) }} días
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar con acciones y historial --}}
    <div class="col-md-4">
        {{-- Panel de acciones del ticket --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Acciones
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    {{-- Botón para editar el ticket --}}
                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar Ticket
                    </a>
                    
                    {{-- Formulario de eliminación con confirmación --}}
                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este ticket? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Eliminar Ticket
                        </button>
                    </form>

                    {{-- Botón para volver a la lista --}}
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-2"></i>Ver Todos los Tickets
                    </a>
                </div>
            </div>
        </div>

        {{-- Panel de historial del ticket --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Historial
                </h5>
            </div>
            <div class="card-body">
                {{-- Timeline del historial de cambios --}}
                <div class="timeline">
                    {{-- Evento de creación del ticket --}}
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Ticket Creado</h6>
                            <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    {{-- Evento de actualización (solo si ha sido modificado) --}}
                    @if($ticket->updated_at != $ticket->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Ticket Actualizado</h6>
                                <small class="text-muted">{{ $ticket->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Estilos CSS para el timeline del historial --}}
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>
@endsection
