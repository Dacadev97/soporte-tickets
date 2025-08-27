# 🎯 Ejercicios Prácticos - Sistema de Tickets

## 📋 Ejercicios por Nivel

### 🟢 Nivel Básico (Principiantes)

---

## Ejercicio 1: Agregar Campo Prioridad

### Objetivo

Agregar un campo "prioridad" a los tickets con valores: baja, media, alta.

### Pasos Detallados

#### 1. Crear Migración

```bash
php artisan make:migration add_priority_to_tickets_table
```

#### 2. Editar la Migración

```php
// database/migrations/xxxx_add_priority_to_tickets_table.php
public function up()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
    });
}

public function down()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('priority');
    });
}
```

#### 3. Actualizar Modelo Ticket

```php
// app/Models/Ticket.php
protected $fillable = [
    'user_id', 'title', 'description', 'status', 'priority'
];

// Agregar accessor para prioridad en español
public function getPriorityTextAttribute()
{
    $priorities = [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta'
    ];

    return $priorities[$this->priority] ?? $this->priority;
}
```

#### 4. Actualizar Controlador

```php
// app/Http/Controllers/TicketController.php
// En los métodos store() y update(), agregar validación:
$validated = $request->validate([
    'user_id' => 'required|exists:users,id',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'status' => 'in:open,in_progress,closed',
    'priority' => 'in:low,medium,high', // Nueva validación
], [
    // ... mensajes existentes
    'priority.in' => 'La prioridad debe ser: baja, media o alta',
]);
```

#### 5. Actualizar Vistas

**Vista Index (resources/views/tickets/index.blade.php):**

```php
<!-- Agregar columna en la tabla -->
<th>Prioridad</th>

<!-- En el bucle foreach -->
<td>
    @if($ticket->priority == 'low')
        <span class="badge bg-success">{{ $ticket->priority_text }}</span>
    @elseif($ticket->priority == 'medium')
        <span class="badge bg-warning">{{ $ticket->priority_text }}</span>
    @else
        <span class="badge bg-danger">{{ $ticket->priority_text }}</span>
    @endif
</td>
```

**Vista Create (resources/views/tickets/create.blade.php):**

```php
<!-- Agregar después del campo status -->
<div class="col-md-6 mb-3">
    <label for="priority" class="form-label">
        <i class="fas fa-exclamation-triangle me-1"></i>Prioridad
    </label>
    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Baja</option>
        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Media</option>
        <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>Alta</option>
    </select>
    @error('priority')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

#### 6. Ejecutar Migración

```bash
php artisan migrate
```

### Resultado Esperado

-   Los tickets ahora tienen un campo de prioridad
-   Se muestra con colores diferentes en la lista
-   Se puede seleccionar al crear/editar tickets

---

## Ejercicio 2: Agregar Filtro por Prioridad

### Objetivo

Permitir filtrar tickets por prioridad en la vista index.

### Pasos

#### 1. Actualizar Controlador

```php
// app/Http/Controllers/TicketController.php
public function index()
{
    $query = Ticket::with('user');

    // Filtro por estado
    if (request('status')) {
        $query->where('status', request('status'));
    }

    // NUEVO: Filtro por prioridad
    if (request('priority')) {
        $query->where('priority', request('priority'));
    }

    // Búsqueda por título
    if (request('search')) {
        $query->where('title', 'like', '%' . request('search') . '%');
    }

    $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
    return view('tickets.index', compact('tickets'));
}
```

#### 2. Actualizar Vista Index

```php
<!-- En el formulario de filtros -->
<div class="col-md-3">
    <label for="priority" class="form-label">Prioridad</label>
    <select name="priority" id="priority" class="form-select">
        <option value="">Todas las prioridades</option>
        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
    </select>
</div>
```

---

### 🟡 Nivel Intermedio

---

## Ejercicio 3: Crear Vista de Estadísticas

### Objetivo

Crear una página que muestre estadísticas de los tickets.

### Pasos

#### 1. Crear Método en Controlador

```php
// app/Http/Controllers/TicketController.php
public function statistics()
{
    $stats = [
        'total' => Ticket::count(),
        'open' => Ticket::where('status', 'open')->count(),
        'in_progress' => Ticket::where('status', 'in_progress')->count(),
        'closed' => Ticket::where('status', 'closed')->count(),
        'high_priority' => Ticket::where('priority', 'high')->count(),
        'by_user' => Ticket::with('user')
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get(),
    ];

    return view('tickets.statistics', compact('stats'));
}
```

#### 2. Crear Vista de Estadísticas

```php
// resources/views/tickets/statistics.blade.php
@extends('layouts.app')

@section('title', 'Estadísticas de Tickets')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-chart-bar me-2"></i>Estadísticas de Tickets
        </h1>
    </div>
</div>

<div class="row">
    <!-- Tarjetas de resumen -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Tickets</h5>
                <h2 class="mb-0">{{ $stats['total'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Abiertos</h5>
                <h2 class="mb-0">{{ $stats['open'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">En Progreso</h5>
                <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Cerrados</h5>
                <h2 class="mb-0">{{ $stats['closed'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tickets por Usuario</h5>
            </div>
            <div class="card-body">
                @foreach($stats['by_user'] as $userStat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $userStat->user->name }}</span>
                        <span class="badge bg-primary">{{ $userStat->total }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Prioridades</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Alta Prioridad</span>
                    <span class="badge bg-danger">{{ $stats['high_priority'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### 3. Agregar Ruta

```php
// routes/web.php
Route::get('/tickets/statistics', [TicketController::class, 'statistics'])->name('tickets.statistics');
```

#### 4. Agregar en Navegación

```php
// resources/views/layouts/app.blade.php
<li class="nav-item">
    <a class="nav-link" href="{{ route('tickets.statistics') }}">
        <i class="fas fa-chart-bar me-1"></i>Estadísticas
    </a>
</li>
```

---

## Ejercicio 4: Búsqueda Avanzada

### Objetivo

Implementar búsqueda por múltiples criterios: título, descripción, usuario.

### Pasos

#### 1. Actualizar Controlador

```php
// app/Http/Controllers/TicketController.php
public function index()
{
    $query = Ticket::with('user');

    // Filtro por estado
    if (request('status')) {
        $query->where('status', request('status'));
    }

    // Filtro por prioridad
    if (request('priority')) {
        $query->where('priority', request('priority'));
    }

    // Búsqueda avanzada
    if (request('search')) {
        $search = request('search');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%');
              });
        });
    }

    // Filtro por fecha
    if (request('date_from')) {
        $query->whereDate('created_at', '>=', request('date_from'));
    }

    if (request('date_to')) {
        $query->whereDate('created_at', '<=', request('date_to'));
    }

    $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
    return view('tickets.index', compact('tickets'));
}
```

#### 2. Actualizar Vista con Filtros Avanzados

```php
<!-- En resources/views/tickets/index.blade.php -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Título, descripción o usuario..." value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Estado</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Abierto</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerrado</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="priority" class="form-label">Prioridad</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">Todas</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="date_from" class="form-label">Desde</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label for="date_to" class="form-label">Hasta</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
```

---

### 🔴 Nivel Avanzado

---

## Ejercicio 5: Implementar Exportación a Excel

### Objetivo

Permitir exportar la lista de tickets a un archivo Excel.

### Pasos

#### 1. Instalar Paquete

```bash
composer require maatwebsite/excel
```

#### 2. Crear Clase de Exportación

```bash
php artisan make:export TicketsExport
```

#### 3. Implementar Exportación

```php
// app/Exports/TicketsExport.php
<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Ticket::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descripción',
            'Usuario',
            'Estado',
            'Prioridad',
            'Fecha Creación',
            'Última Actualización'
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->title,
            $ticket->description,
            $ticket->user->name,
            $ticket->status_text,
            $ticket->priority_text,
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->updated_at->format('d/m/Y H:i')
        ];
    }
}
```

#### 4. Agregar Método en Controlador

```php
// app/Http/Controllers/TicketController.php
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;

public function export()
{
    return Excel::download(new TicketsExport, 'tickets.xlsx');
}
```

#### 5. Agregar Ruta

```php
// routes/web.php
Route::get('/tickets/export', [TicketController::class, 'export'])->name('tickets.export');
```

#### 6. Agregar Botón en Vista

```php
<!-- En resources/views/tickets/index.blade.php -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-ticket-alt me-2"></i>Lista de Tickets
    </h1>
    <div>
        <a href="{{ route('tickets.export') }}" class="btn btn-success me-2">
            <i class="fas fa-file-excel me-2"></i>Exportar Excel
        </a>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Ticket
        </a>
    </div>
</div>
```

---

## Ejercicio 6: Implementar Notificaciones en Tiempo Real

### Objetivo

Mostrar notificaciones cuando se crea o actualiza un ticket.

### Pasos

#### 1. Crear Evento

```bash
php artisan make:event TicketCreated
```

#### 2. Implementar Evento

```php
// app/Events/TicketCreated.php
<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }

    public function broadcastAs()
    {
        return 'ticket.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'user' => $this->ticket->user->name,
            'status' => $this->ticket->status_text
        ];
    }
}
```

#### 3. Disparar Evento en Controlador

```php
// app/Http/Controllers/TicketController.php
use App\Events\TicketCreated;

public function store(Request $request)
{
    $validated = $request->validate([...]);
    $ticket = Ticket::create($validated);

    // Disparar evento
    event(new TicketCreated($ticket));

    return redirect()->route('tickets.index')->with('success', 'Ticket creado exitosamente');
}
```

#### 4. Agregar JavaScript para Escuchar Eventos

```javascript
// En resources/views/layouts/app.blade.php
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('YOUR_PUSHER_KEY', {
        cluster: 'YOUR_CLUSTER'
    });

    const channel = pusher.subscribe('tickets');
    channel.bind('ticket.created', function(data) {
        // Mostrar notificación
        showNotification('Nuevo ticket creado: ' + data.title);
    });

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
</script>
```

---

## 📊 Evaluación de Ejercicios

### Criterios de Evaluación:

#### Nivel Básico (40 puntos)

-   ✅ Migración creada correctamente (10 pts)
-   ✅ Modelo actualizado (10 pts)
-   ✅ Controlador modificado (10 pts)
-   ✅ Vistas actualizadas (10 pts)

#### Nivel Intermedio (30 puntos)

-   ✅ Estadísticas implementadas (15 pts)
-   ✅ Búsqueda avanzada funcionando (15 pts)

#### Nivel Avanzado (30 puntos)

-   ✅ Exportación a Excel (15 pts)
-   ✅ Notificaciones en tiempo real (15 pts)

### Puntos Extra (10 puntos)

-   🎨 Diseño mejorado
-   📱 Responsive design
-   🔒 Validaciones adicionales
-   ⚡ Optimizaciones de rendimiento

---

## 🎯 Desafíos Adicionales

### Desafío 1: Sistema de Comentarios

Implementar un sistema de comentarios en los tickets.

### Desafío 2: Adjuntar Archivos

Permitir adjuntar archivos a los tickets.

### Desafío 3: Dashboard con Gráficos

Crear un dashboard con gráficos usando Chart.js.

### Desafío 4: API REST

Crear una API REST para el sistema de tickets.

### Desafío 5: Tests Unitarios

Implementar tests unitarios para el controlador y modelo.

---

## 📝 Notas para el Instructor

### Consejos para los Ejercicios:

1. **Empezar con el nivel básico** y progresar gradualmente
2. **Dar tiempo suficiente** para cada ejercicio
3. **Proporcionar ayuda** cuando sea necesario
4. **Revisar el código** de los estudiantes
5. **Fomentar la colaboración** entre estudiantes

### Solución de Problemas:

-   Verificar que las migraciones se ejecuten correctamente
-   Comprobar que los nombres de rutas sean correctos
-   Asegurar que las vistas estén en las carpetas correctas
-   Verificar la sintaxis de Blade

### Recursos Adicionales:

-   [Documentación de Laravel](https://laravel.com/docs)
-   [Laravel Excel](https://laravel-excel.com/)
-   [Pusher](https://pusher.com/)
-   [Bootstrap](https://getbootstrap.com/)
