@extends('layouts.app')

@section('title', 'Editar Ticket #' . $ticket->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
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
            <div class="card-body">
                <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
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
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Proporcione una descripción detallada del problema o solicitud.
                        </div>
                    </div>

                    <!-- Información del ticket -->
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

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Ver Todos
                            </a>
                        </div>
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
