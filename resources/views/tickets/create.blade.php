{{-- 
    Vista: Crear Nuevo Ticket
    ========================
    
    Esta vista muestra el formulario para crear un nuevo ticket de soporte.
    Incluye:
    - Formulario completo con validación del lado del cliente
    - Selección de usuario asignado
    - Selección de estado inicial
    - Campos para título y descripción
    - Validación visual con Bootstrap
    - Mensajes de error personalizados
    
    Variables disponibles:
    - $users: Colección de usuarios disponibles para asignar
--}}

@extends('layouts.app')

@section('title', 'Crear Nuevo Ticket')

@section('content')
{{-- Contenedor principal centrado --}}
<div class="row justify-content-center">
    <div class="col-md-8">
        {{-- Tarjeta del formulario --}}
        <div class="card">
            {{-- Encabezado de la tarjeta --}}
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Crear Nuevo Ticket
                </h4>
            </div>
            {{-- Cuerpo de la tarjeta con el formulario --}}
            <div class="card-body">
                {{-- Formulario que envía datos al método store del controlador --}}
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    
                    {{-- Primera fila: Usuario y Estado --}}
                    <div class="row">
                        {{-- Campo de selección de usuario --}}
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">
                                <i class="fas fa-user me-1"></i>Usuario <span class="text-danger">*</span>
                            </label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Seleccionar usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo de selección de estado --}}
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-1"></i>Estado
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Abierto</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Campo de título del ticket --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1"></i>Título <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="Ingrese el título del ticket" 
                               maxlength="255" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Campo de descripción del ticket --}}
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Describa el problema o solicitud del ticket" 
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- Texto de ayuda para el usuario --}}
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Proporcione una descripción detallada del problema o solicitud.
                        </div>
                    </div>

                    {{-- Botones de acción del formulario --}}
                    <div class="d-flex justify-content-between">
                        {{-- Botón para cancelar y volver a la lista --}}
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        {{-- Botón para enviar el formulario --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
