{{-- 
    Vista: Lista de Tickets (Index)
    ===============================
    
    Esta vista muestra la lista principal de todos los tickets del sistema.
    Incluye funcionalidades de:
    - Filtrado por estado (abierto, en progreso, cerrado)
    - Búsqueda por título de ticket
    - Paginación automática
    - Acciones CRUD para cada ticket
    - Diseño responsive con Bootstrap
    
    Variables disponibles:
    - $tickets: Colección paginada de tickets con relación de usuario
--}}

@extends('layouts.app')

@section('title', 'Lista de Tickets')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Encabezado de la página con título y botón de creación --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-ticket-alt me-2"></i>Lista de Tickets
            </h1>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Ticket
            </a>
        </div>

        {{-- Panel de filtros y búsqueda --}}
        <div class="card mb-4">
            <div class="card-body">
                {{-- Formulario de filtros que envía parámetros GET --}}
                <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
                    {{-- Filtro por estado del ticket --}}
                    <div class="col-md-4">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Abierto</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>
                    {{-- Campo de búsqueda por título --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Buscar por título..." value="{{ request('search') }}">
                    </div>
                    {{-- Botones de acción para filtros --}}
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla principal de tickets --}}
        <div class="card">
            <div class="card-body">
                @if($tickets->count() > 0)
                    {{-- Tabla responsive para mostrar tickets --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            {{-- Encabezados de la tabla --}}
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            {{-- Cuerpo de la tabla con datos de tickets --}}
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        {{-- ID del ticket --}}
                                        <td>#{{ $ticket->id }}</td>
                                        
                                        {{-- Título y descripción del ticket --}}
                                        <td>
                                            <strong>{{ $ticket->title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($ticket->description, 50) }}
                                            </small>
                                        </td>
                                        
                                        {{-- Usuario asignado al ticket --}}
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-user me-1"></i>{{ $ticket->user->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        
                                        {{-- Estado del ticket con colores diferenciados --}}
                                        <td>
                                            @if($ticket->status == 'open')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>{{ $ticket->status_text }}
                                                </span>
                                            @elseif($ticket->status == 'in_progress')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-spinner me-1"></i>{{ $ticket->status_text }}
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>{{ $ticket->status_text }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                        {{-- Fecha de creación formateada --}}
                                        <td>
                                            <small class="text-muted">
                                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        
                                        {{-- Botones de acciones CRUD --}}
                                        <td>
                                            <div class="btn-group" role="group">
                                                {{-- Botón Ver detalles --}}
                                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- Botón Editar --}}
                                                <a href="{{ route('tickets.edit', $ticket->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Formulario de eliminación con confirmación --}}
                                                <form action="{{ route('tickets.destroy', $ticket->id) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este ticket?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación automática de Laravel --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tickets->links() }}
                    </div>
                @else
                    {{-- Estado vacío cuando no hay tickets --}}
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay tickets</h4>
                        <p class="text-muted">No se encontraron tickets que coincidan con los criterios de búsqueda.</p>
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Crear primer ticket
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
