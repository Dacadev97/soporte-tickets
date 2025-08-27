# 📚 Documentación Completa - Sistema de Tickets de Soporte

## 🎯 Objetivo del Proyecto

Este proyecto es un **Sistema de Gestión de Tickets de Soporte** desarrollado con Laravel 10. Sirve como ejemplo completo para aprender:

-   **Arquitectura MVC** en Laravel
-   **CRUD completo** (Create, Read, Update, Delete)
-   **Relaciones entre modelos**
-   **Validación de formularios**
-   **Paginación y filtros**
-   **Vistas Blade con Bootstrap**
-   **Seeders y migraciones**

---

## 🏗️ Arquitectura del Proyecto

### Estructura de Directorios

```
soporte-tickets/
├── app/
│   ├── Http/Controllers/
│   │   └── TicketController.php    # Controlador principal
│   └── Models/
│       ├── Ticket.php             # Modelo de tickets
│       └── User.php               # Modelo de usuarios
├── database/
│   ├── migrations/
│   │   └── 2025_08_26_233902_tickets.php
│   └── seeders/
│       ├── UserSeeder.php
│       ├── TicketSeeder.php
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php      # Layout principal
│       └── tickets/
│           ├── index.blade.php    # Lista de tickets
│           ├── create.blade.php   # Crear ticket
│           ├── show.blade.php     # Ver ticket
│           └── edit.blade.php     # Editar ticket
└── routes/
    └── web.php                    # Rutas web
```

---

## 🗄️ Base de Datos

### Migración de Tickets

```php
// database/migrations/2025_08_26_233902_tickets.php
Schema::create('tickets', function (Blueprint $table) {
    $table->id();                                    // ID autoincremental
    $table->foreignId('user_id')->constrained();     // Relación con usuarios
    $table->string('title');                         // Título del ticket
    $table->text('description');                     // Descripción detallada
    $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
    $table->timestamps();                            // created_at, updated_at
});
```

### Campos de la Tabla

| Campo         | Tipo         | Descripción                       |
| ------------- | ------------ | --------------------------------- |
| `id`          | BIGINT       | Clave primaria autoincremental    |
| `user_id`     | BIGINT       | Clave foránea a la tabla users    |
| `title`       | VARCHAR(255) | Título del ticket                 |
| `description` | TEXT         | Descripción detallada             |
| `status`      | ENUM         | Estado: open, in_progress, closed |
| `created_at`  | TIMESTAMP    | Fecha de creación                 |
| `updated_at`  | TIMESTAMP    | Fecha de última actualización     |

---

## 🎭 Modelos (Models)

### Modelo Ticket

```php
// app/Models/Ticket.php
class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'status'
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes para filtrado
    public function scopeOpen($query) { ... }
    public function scopeInProgress($query) { ... }
    public function scopeClosed($query) { ... }

    // Accessor para estado en español
    public function getStatusTextAttribute() { ... }
}
```

### Conceptos Clave:

1. **Fillable**: Campos que se pueden asignar masivamente
2. **Relaciones**: `belongsTo` para la relación con User
3. **Scopes**: Métodos para filtrar consultas
4. **Accessors**: Métodos para formatear datos

---

## 🎮 Controlador (Controller)

### TicketController - Métodos CRUD

```php
// app/Http/Controllers/TicketController.php

class TicketController extends Controller
{
    // INDEX - Listar tickets
    public function index()
    {
        $query = Ticket::with('user');

        // Filtros
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Búsqueda
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    // CREATE - Mostrar formulario
    public function create()
    {
        $users = User::all();
        return view('tickets.create', compact('users'));
    }

    // STORE - Guardar ticket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'in:open,in_progress,closed',
        ]);

        Ticket::create($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket creado');
    }

    // SHOW - Mostrar ticket
    public function show(string $id)
    {
        $ticket = Ticket::with('user')->findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    // EDIT - Mostrar formulario de edición
    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $users = User::all();
        return view('tickets.edit', compact('ticket', 'users'));
    }

    // UPDATE - Actualizar ticket
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $validated = $request->validate([...]);
        $ticket->update($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado');
    }

    // DESTROY - Eliminar ticket
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado');
    }
}
```

### Conceptos Importantes:

1. **Resource Controller**: Laravel genera automáticamente los 7 métodos CRUD
2. **Eager Loading**: `with('user')` para evitar el problema N+1
3. **Validación**: Reglas de validación con mensajes personalizados
4. **Flash Messages**: Mensajes de sesión para feedback al usuario
5. **FindOrFail**: Lanza 404 si no encuentra el registro

---

## 🛣️ Rutas (Routes)

### Resource Routes

```php
// routes/web.php
Route::resource('tickets', TicketController::class);
```

Esta línea genera automáticamente las siguientes rutas:

| Método    | URI                      | Nombre          | Acción    |
| --------- | ------------------------ | --------------- | --------- |
| GET       | `/tickets`               | tickets.index   | index()   |
| GET       | `/tickets/create`        | tickets.create  | create()  |
| POST      | `/tickets`               | tickets.store   | store()   |
| GET       | `/tickets/{ticket}`      | tickets.show    | show()    |
| GET       | `/tickets/{ticket}/edit` | tickets.edit    | edit()    |
| PUT/PATCH | `/tickets/{ticket}`      | tickets.update  | update()  |
| DELETE    | `/tickets/{ticket}`      | tickets.destroy | destroy() |

---

## 🎨 Vistas (Views)

### Layout Principal

```php
// resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('title', 'Sistema de Tickets')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <!-- Navegación -->
    </nav>

    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
```

### Conceptos de Blade:

1. **@extends**: Herencia de layouts
2. **@yield**: Secciones que se pueden rellenar
3. **@if/@endif**: Condicionales
4. **@foreach/@endforeach**: Bucles
5. **{{ }}**: Escape automático de HTML
6. **{!! !!}**: Sin escape (usar con cuidado)

### Vista Index (Lista)

```php
// resources/views/tickets/index.blade.php
@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <form method="GET" action="{{ route('tickets.index') }}">
            <select name="status">
                <option value="">Todos los estados</option>
                <option value="open">Abierto</option>
                <option value="in_progress">En Progreso</option>
                <option value="closed">Cerrado</option>
            </select>
            <input type="text" name="search" placeholder="Buscar...">
            <button type="submit">Filtrar</button>
        </form>

        <!-- Tabla -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->id }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ $ticket->user->name }}</td>
                        <td>
                            @if($ticket->status == 'open')
                                <span class="badge bg-warning">Abierto</span>
                            @elseif($ticket->status == 'in_progress')
                                <span class="badge bg-primary">En Progreso</span>
                            @else
                                <span class="badge bg-success">Cerrado</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}">Ver</a>
                            <a href="{{ route('tickets.edit', $ticket->id) }}">Editar</a>
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        {{ $tickets->links() }}
    </div>
</div>
@endsection
```

---

## 🌱 Seeders (Datos de Prueba)

### UserSeeder

```php
// database/seeders/UserSeeder.php
public function run(): void
{
    $users = [
        [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('password'),
        ],
        // ... más usuarios
    ];

    foreach ($users as $userData) {
        User::create($userData);
    }
}
```

### TicketSeeder

```php
// database/seeders/TicketSeeder.php
public function run(): void
{
    $users = User::all();

    $tickets = [
        [
            'user_id' => $users->random()->id,
            'title' => 'Problema con el acceso al sistema',
            'description' => 'No puedo acceder al sistema...',
            'status' => 'open',
        ],
        // ... más tickets
    ];

    foreach ($tickets as $ticketData) {
        Ticket::create($ticketData);
    }
}
```

---

## 🔧 Comandos Artisan Importantes

```bash
# Crear migración
php artisan make:migration create_tickets_table

# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Revertir y ejecutar (fresh)
php artisan migrate:fresh

# Crear seeder
php artisan make:seeder UserSeeder

# Ejecutar seeders
php artisan db:seed

# Crear controlador
php artisan make:controller TicketController --resource

# Crear modelo
php artisan make:model Ticket

# Crear modelo con migración
php artisan make:model Ticket -m

# Listar rutas
php artisan route:list

# Iniciar servidor
php artisan serve
```

---

## 🎯 Conceptos Clave para la Clase

### 1. **MVC (Model-View-Controller)**

-   **Model**: Lógica de negocio y acceso a datos (Ticket.php, User.php)
-   **View**: Interfaz de usuario (archivos .blade.php)
-   **Controller**: Lógica de control (TicketController.php)

### 2. **Eloquent ORM**

-   Mapeo objeto-relacional
-   Relaciones entre modelos
-   Scopes y accessors
-   Mass assignment protection

### 3. **Validación**

-   Reglas de validación
-   Mensajes personalizados
-   Redirección con errores
-   Old input preservation

### 4. **Blade Templates**

-   Herencia de layouts
-   Directivas (@if, @foreach, etc.)
-   Escape de datos
-   Componentes reutilizables

### 5. **Rutas Resource**

-   Generación automática de rutas CRUD
-   Convenciones de Laravel
-   Nombres de rutas

### 6. **Paginación**

-   Paginación automática
-   Personalización de vistas
-   Mantener filtros en paginación

---

## 🚀 Flujo de Trabajo del Proyecto

### 1. **Planificación**

-   Definir entidades (User, Ticket)
-   Diseñar relaciones
-   Planificar funcionalidades

### 2. **Base de Datos**

-   Crear migraciones
-   Definir campos y tipos
-   Establecer relaciones

### 3. **Modelos**

-   Definir fillable
-   Crear relaciones
-   Agregar scopes y accessors

### 4. **Controladores**

-   Implementar métodos CRUD
-   Agregar validación
-   Manejar respuestas

### 5. **Vistas**

-   Crear layout base
-   Implementar formularios
-   Agregar filtros y búsqueda

### 6. **Rutas**

-   Configurar resource routes
-   Agregar rutas adicionales

### 7. **Datos de Prueba**

-   Crear seeders
-   Generar datos realistas

### 8. **Testing**

-   Probar todas las funcionalidades
-   Verificar validaciones
-   Comprobar UX

---

## 📝 Ejercicios Prácticos para la Clase

### Nivel Básico

1. Agregar un campo "prioridad" a los tickets
2. Crear una vista para mostrar estadísticas
3. Agregar validación para el campo título

### Nivel Intermedio

1. Implementar búsqueda por usuario
2. Agregar filtro por fecha de creación
3. Crear un dashboard con gráficos

### Nivel Avanzado

1. Implementar autenticación de usuarios
2. Agregar roles y permisos
3. Crear API REST para tickets

---

## 🔍 Puntos de Discusión

### 1. **¿Por qué usar Resource Controllers?**

-   Convención sobre configuración
-   Código más limpio y mantenible
-   Rutas automáticas

### 2. **¿Cuándo usar Eager Loading?**

-   Evitar el problema N+1
-   Mejorar rendimiento
-   Cuándo cargar relaciones

### 3. **¿Cómo manejar la validación?**

-   Reglas de validación
-   Mensajes personalizados
-   Validación en el frontend vs backend

### 4. **¿Qué ventajas tiene Blade?**

-   Sintaxis simple
-   Herencia de templates
-   Integración con Laravel

---

## 📚 Recursos Adicionales

-   [Documentación oficial de Laravel](https://laravel.com/docs)
-   [Eloquent ORM](https://laravel.com/docs/eloquent)
-   [Blade Templates](https://laravel.com/docs/blade)
-   [Validation](https://laravel.com/docs/validation)
-   [Database Migrations](https://laravel.com/docs/migrations)

---

## 🎉 Conclusión

Este proyecto demuestra las mejores prácticas de Laravel para crear un CRUD completo. Los estudiantes aprenderán:

-   Arquitectura MVC
-   Relaciones entre modelos
-   Validación de datos
-   Creación de vistas
-   Manejo de rutas
-   Uso de seeders

El código está bien estructurado, comentado y sigue las convenciones de Laravel, lo que lo hace perfecto para fines educativos.
