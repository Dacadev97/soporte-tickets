@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Lista de Tickets</h1>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtros y búsqueda -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('tickets.index') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por título..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group mr-3">
                            <select name="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Abierto</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </form>
                </div>
            </div>

            <!-- Botón para crear nuevo ticket -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('tickets.create') }}" class="btn btn-success">Crear Nuevo Ticket</a>
                </div>
            </div>

            <!-- Lista de tickets -->
            @if($tickets->count() > 0)
                <div class="row">
                    @foreach($tickets as $ticket)
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('tickets.show', $ticket->id) }}">{{ $ticket->title }}</a>
                                    </h5>
                                    <p class="card-text">
                                        <strong>Usuario:</strong> {{ $ticket->user->name }}<br>
                                        <strong>Estado:</strong> 
                                        <span class="badge badge-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ $ticket->status_text }}
                                        </span><br>
                                        <strong>Descripción:</strong> {{ Str::limit($ticket->description, 100) }}<br>
                                        <strong>Creado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-info btn-sm">Ver</a>
                                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este ticket?')">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="row">
                    <div class="col-md-12">
                        {{ $tickets->links() }}
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <h4>No hay tickets disponibles</h4>
                    <p>No se encontraron tickets que coincidan con los criterios de búsqueda.</p>
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">Crear el primer ticket</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
