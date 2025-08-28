{{-- 
    Vista: Editar Ticket
    ===================
    
    Esta vista muestra el formulario para editar un ticket existente.
    Incluye:
    - Formulario pre-poblado con los datos actuales del ticket
    - Validación del lado del cliente y servidor
    - Selección de usuario y estado
    - Campos para título y descripción
    - Información de fechas de creación y actualización
    - Botones de navegación y acción
    
    Variables disponibles:
    - $ticket: Modelo Ticket con datos actuales
    - $users: Colección de usuarios disponibles para asignar
--}}

@extends('layouts.app')

@section('title', 'Editar Ticket #' . $ticket->id)

@section('content')
{{-- Contenedor principal centrado --}}
<div class="row justify-content-center">
    <div class="col-md-8">
        {{-- Tarjeta del formulario de edición --}}
        <div class="card">
            {{-- Encabezado con título y botón para ver ticket --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Ticket #{{ $ticket->id }}
                    </h4>
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eye me-1"></i>Ver Ticket
                    </a>
                </div>
            </div>
            {{-- Cuerpo de la tarjeta con el formulario --}}
            <div class="card-body">
                {{-- Formulario que envía datos al método update del controlador --}}
                <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- Primera fila: Usuario y Estado --}}
                    <div class="row">
                        {{-- Campo de selección de usuario (pre-seleccionado) --}}
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">
                                <i class="fas fa-user me-1"></i>Usuario <span class="text-danger">*</span>
                            </label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Seleccionar usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('user_id', $ticket->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo de selección de estado (pre-seleccionado) --}}
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-1"></i>Estado
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Abierto</option>
                                <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Campo de título del ticket (pre-poblado) --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1"></i>Título <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $ticket->title) }}" 
                               placeholder="Ingrese el título del ticket" 
                               maxlength="255" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Campo de descripción del ticket (pre-poblado) --}}
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Describa el problema o solicitud del ticket" 
                                  required>{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- Texto de ayuda para el usuario --}}
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Proporcione una descripción detallada del problema o solicitud.
                        </div>
                    </div>

                    {{-- Panel informativo con fechas del ticket --}}
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Información del Ticket
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    <strong>Creado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    <strong>Última actualización:</strong> {{ $ticket->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de acción del formulario --}}
                    <div class="d-flex justify-content-between">
                        <div>
                            {{-- Botón para cancelar y volver a ver el ticket --}}
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            {{-- Botón para ir a la lista de tickets --}}
                            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Ver Todos
                            </a>
                        </div>
                        {{-- Botón para enviar el formulario de actualización --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
