# 🎓 Presentación para la Clase - Sistema de Tickets

## 📋 Estructura de la Presentación

### 🎯 Slide 1: Título

```
Sistema de Tickets de Soporte
Desarrollado con Laravel 10

🎓 Clase Práctica de Desarrollo Web
```

### 🎯 Slide 2: Objetivos de la Clase

```
Al final de esta clase podrás:

✅ Comprender la arquitectura MVC de Laravel
✅ Implementar un CRUD completo desde cero
✅ Trabajar con relaciones entre modelos
✅ Validar formularios correctamente
✅ Crear vistas Blade con Bootstrap
✅ Usar seeders para datos de prueba
```

### 🎯 Slide 3: ¿Qué es un Sistema de Tickets?

```
Un sistema de tickets es una aplicación que permite:

📝 Crear solicitudes de soporte
👥 Asignar tickets a usuarios
📊 Gestionar estados (Abierto, En Progreso, Cerrado)
🔍 Buscar y filtrar tickets
📈 Generar reportes y estadísticas
```

### 🎯 Slide 4: Arquitectura MVC

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    Model    │    │ Controller  │    │    View     │
│             │    │             │    │             │
│ - Datos     │◄──►│ - Lógica    │◄──►│ - Interfaz  │
│ - Reglas    │    │ - Control   │    │ - Usuario   │
└─────────────┘    └─────────────┘    └─────────────┘

Model: Lógica de negocio y acceso a datos
Controller: Maneja las peticiones y respuestas
View: Interfaz de usuario
```

### 🎯 Slide 5: Estructura del Proyecto

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
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       └── tickets/
└── routes/
    └── web.php
```

### 🎯 Slide 6: Base de Datos

```
Tabla: tickets
├── id (BIGINT, PRIMARY KEY)
├── user_id (BIGINT, FOREIGN KEY)
├── title (VARCHAR(255))
├── description (TEXT)
├── status (ENUM: open, in_progress, closed)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

Relación: Un ticket pertenece a un usuario
```

### 🎯 Slide 7: Modelo Ticket

```php
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

    // Accessor para estado en español
    public function getStatusTextAttribute()
    {
        $statuses = [
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'closed' => 'Cerrado'
        ];
        return $statuses[$this->status] ?? $this->status;
    }
}
```

### 🎯 Slide 8: Controlador - Métodos CRUD

```php
class TicketController extends Controller
{
    public function index()    // Listar tickets
    public function create()   // Mostrar formulario crear
    public function store()    // Guardar ticket
    public function show()     // Mostrar ticket
    public function edit()     // Mostrar formulario editar
    public function update()   // Actualizar ticket
    public function destroy()  // Eliminar ticket
}
```

### 🎯 Slide 9: Método Index con Filtros

```php
public function index()
{
    $query = Ticket::with('user');  // Eager Loading

    // Filtro por estado
    if (request('status')) {
        $query->where('status', request('status'));
    }

    // Búsqueda por título
    if (request('search')) {
        $query->where('title', 'like', '%' . request('search') . '%');
    }

    $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
    return view('tickets.index', compact('tickets'));
}
```

### 🎯 Slide 10: Validación

```php
$validated = $request->validate([
    'user_id' => 'required|exists:users,id',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'status' => 'in:open,in_progress,closed',
], [
    'user_id.required' => 'El usuario es obligatorio',
    'title.required' => 'El título es obligatorio',
    'description.required' => 'La descripción es obligatoria',
]);
```

### 🎯 Slide 11: Rutas Resource

```php
Route::resource('tickets', TicketController::class);
```

```
Genera automáticamente:
GET    /tickets           → index()
GET    /tickets/create    → create()
POST   /tickets           → store()
GET    /tickets/{id}      → show()
GET    /tickets/{id}/edit → edit()
PUT    /tickets/{id}      → update()
DELETE /tickets/{id}      → destroy()
```

### 🎯 Slide 12: Vistas Blade

```php
@extends('layouts.app')

@section('title', 'Lista de Tickets')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Contenido de la vista -->
        @foreach($tickets as $ticket)
            <div class="card">
                <h5>{{ $ticket->title }}</h5>
                <p>{{ $ticket->description }}</p>
                <span class="badge bg-{{ $ticket->status == 'open' ? 'warning' : 'success' }}">
                    {{ $ticket->status_text }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endsection
```

### 🎯 Slide 13: Layout Principal

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('title', 'Sistema de Tickets')</title>
    <link href="bootstrap.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <!-- Navegación -->
    </nav>

    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
```

### 🎯 Slide 14: Seeders - Datos de Prueba

```php
// UserSeeder.php
$users = [
    ['name' => 'Juan Pérez', 'email' => 'juan@example.com'],
    ['name' => 'María García', 'email' => 'maria@example.com'],
    // ... más usuarios
];

// TicketSeeder.php
$tickets = [
    [
        'user_id' => $users->random()->id,
        'title' => 'Problema con el acceso al sistema',
        'description' => 'No puedo acceder al sistema...',
        'status' => 'open',
    ],
    // ... más tickets
];
```

### 🎯 Slide 15: Comandos Artisan Importantes

```bash
# Crear controlador
php artisan make:controller TicketController --resource

# Crear migración
php artisan make:migration create_tickets_table

# Ejecutar migraciones
php artisan migrate:fresh --seed

# Listar rutas
php artisan route:list

# Iniciar servidor
php artisan serve
```

### 🎯 Slide 16: Funcionalidades del Sistema

```
✅ CRUD completo de tickets
✅ Gestión de usuarios
✅ Estados de tickets (Abierto, En Progreso, Cerrado)
✅ Búsqueda y filtrado
✅ Paginación
✅ Validación de formularios
✅ Interfaz moderna con Bootstrap
✅ Mensajes de confirmación
```

### 🎯 Slide 17: Estados de Tickets

```
🟡 Abierto (open)
- Ticket recién creado
- Necesita atención

🔵 En Progreso (in_progress)
- Ticket siendo atendido
- En proceso de resolución

🟢 Cerrado (closed)
- Ticket resuelto
- Problema solucionado
```

### 🎯 Slide 18: Filtros y Búsqueda

```
🔍 Búsqueda por título
📊 Filtro por estado
👥 Filtro por usuario
📅 Ordenamiento por fecha
📄 Paginación (10 tickets por página)
```

### 🎯 Slide 19: Validación de Formularios

```
✅ Campos obligatorios
✅ Longitud máxima de campos
✅ Valores permitidos en enums
✅ Existencia de relaciones
✅ Mensajes personalizados en español
✅ Validación en frontend y backend
```

### 🎯 Slide 20: Tecnologías Utilizadas

```
🟠 Backend: Laravel 10
🎨 Frontend: Bootstrap 5
📊 Base de datos: MySQL/PostgreSQL/SQLite
🔍 Validación: Laravel Validation
📄 Paginación: Laravel Pagination
🎭 Templates: Blade
```

### 🎯 Slide 21: Ejercicios Prácticos

```
🟢 Nivel Básico:
- Agregar campo prioridad
- Implementar filtros adicionales

🟡 Nivel Intermedio:
- Crear vista de estadísticas
- Búsqueda avanzada

🔴 Nivel Avanzado:
- Exportación a Excel
- Notificaciones en tiempo real
```

### 🎯 Slide 22: Conceptos Clave Aprendidos

```
🎯 Arquitectura MVC
🎯 Relaciones Eloquent
🎯 Resource Controllers
🎯 Validación de datos
🎯 Vistas Blade
🎯 Migraciones y Seeders
🎯 Rutas RESTful
🎯 Eager Loading
```

### 🎯 Slide 23: Próximos Pasos

```
🚀 Implementar autenticación
🔐 Agregar roles y permisos
📱 Crear API REST
🔔 Implementar notificaciones
🧪 Agregar tests unitarios
📊 Dashboard con gráficos
📧 Notificaciones por email
```

### 🎯 Slide 24: Recursos de Aprendizaje

```
📚 Documentación oficial: laravel.com/docs
🎥 Laracasts: laracasts.com
📰 Laravel News: laravel-news.com
💬 Stack Overflow: stackoverflow.com
🐦 Twitter: @laravelphp
📺 YouTube: Laravel Tutorials
```

### 🎯 Slide 25: Preguntas y Respuestas

```
❓ ¿Por qué usar Resource Controllers?
❓ ¿Cuándo usar Eager Loading?
❓ ¿Cómo manejar la validación?
❓ ¿Qué ventajas tiene Blade?
❓ ¿Cómo optimizar el rendimiento?
```

### 🎯 Slide 26: Demo en Vivo

```
🎬 Mostrar el sistema funcionando:

1. Lista de tickets
2. Crear nuevo ticket
3. Editar ticket existente
4. Filtrar y buscar
5. Ver detalles de ticket
6. Eliminar ticket
```

### 🎯 Slide 27: Código en Acción

```
💻 Mostrar código en el editor:

1. Controlador TicketController
2. Modelo Ticket
3. Vista index.blade.php
4. Migración de tickets
5. Rutas web.php
```

### 🎯 Slide 28: Evaluación

```
📊 Criterios de evaluación:

25% - Comprensión de MVC
30% - Implementación de CRUD
20% - Uso de relaciones
15% - Validación de datos
10% - Interfaz de usuario
```

### 🎯 Slide 29: Resumen

```
✅ Hemos creado un sistema completo de tickets
✅ Implementamos CRUD completo
✅ Aprendimos arquitectura MVC
✅ Trabajamos con relaciones
✅ Validamos formularios
✅ Creamos vistas modernas
```

### 🎯 Slide 30: ¡Gracias!

```
🎓 ¡Gracias por tu atención!

¿Preguntas?

📧 Contacto: tu-email@ejemplo.com
🌐 GitHub: github.com/tu-usuario
💼 LinkedIn: linkedin.com/in/tu-perfil

¡Sigue aprendiendo Laravel! 🚀
```

---

## 📝 Notas para la Presentación

### Consejos para el Presentador:

1. **Tiempo estimado:** 45-60 minutos
2. **Mostrar el código funcionando** desde el principio
3. **Interactuar con los estudiantes** durante la presentación
4. **Usar ejemplos reales** y casos de uso
5. **Animar preguntas** y participación

### Materiales Necesarios:

-   ✅ Proyecto funcionando en local
-   ✅ Base de datos con datos de prueba
-   ✅ Editor de código abierto
-   ✅ Navegador web
-   ✅ Terminal/consola

### Puntos Clave a Destacar:

1. **Arquitectura MVC** - Explicar por qué es importante
2. **Resource Controllers** - Convención sobre configuración
3. **Relaciones Eloquent** - Eager Loading y N+1
4. **Validación** - Seguridad y UX
5. **Blade Templates** - Herencia y reutilización

### Transiciones Sugeridas:

-   Slide 1-5: Introducción y conceptos
-   Slide 6-10: Base de datos y modelos
-   Slide 11-15: Controladores y validación
-   Slide 16-20: Vistas y funcionalidades
-   Slide 21-25: Ejercicios y recursos
-   Slide 26-30: Demo y cierre

Esta presentación te dará una estructura completa para dictar la clase de manera efectiva y profesional.
