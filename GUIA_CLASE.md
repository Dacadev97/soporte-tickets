# 🎓 Guía para Dictar Clase - Sistema de Tickets

## 📋 Preparación de la Clase

### Materiales Necesarios

-   Proyecto funcionando en local
-   Base de datos con datos de prueba
-   Presentación de diapositivas (opcional)
-   Ejercicios prácticos preparados

### Tiempo Estimado: 2-3 horas

---

## 🎯 Objetivos de Aprendizaje

### Al final de la clase, los estudiantes podrán:

1. **Comprender la arquitectura MVC** de Laravel
2. **Implementar un CRUD completo** desde cero
3. **Trabajar con relaciones** entre modelos
4. **Validar formularios** correctamente
5. **Crear vistas Blade** con Bootstrap
6. **Usar seeders** para datos de prueba

---

## 📚 Estructura de la Clase

### Parte 1: Introducción (30 minutos)

#### 1.1 Presentación del Proyecto

```bash
# Mostrar el proyecto funcionando
php artisan serve
# Navegar a http://localhost:8000
```

**Preguntas para los estudiantes:**

-   ¿Qué es un sistema de tickets?
-   ¿Qué funcionalidades creen que necesita?
-   ¿Cómo organizarían la base de datos?

#### 1.2 Arquitectura MVC

**Explicar con diagrama:**

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    Model    │    │ Controller  │    │    View     │
│             │    │             │    │             │
│ - Datos     │◄──►│ - Lógica    │◄──►│ - Interfaz  │
│ - Reglas    │    │ - Control   │    │ - Usuario   │
└─────────────┘    └─────────────┘    └─────────────┘
```

### Parte 2: Base de Datos (45 minutos)

#### 2.1 Diseño de la Base de Datos

**Mostrar la migración:**

```php
// database/migrations/2025_08_26_233902_tickets.php
Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->text('description');
    $table->enum('status', ['open', 'in_progress', 'closed']);
    $table->timestamps();
});
```

**Conceptos a explicar:**

-   Claves primarias y foráneas
-   Tipos de datos
-   Constraints
-   Timestamps automáticos

#### 2.2 Ejecutar Migraciones

```bash
php artisan migrate:fresh --seed
```

**Mostrar en la base de datos:**

-   Tabla `users`
-   Tabla `tickets`
-   Relación entre tablas

### Parte 3: Modelos (30 minutos)

#### 3.1 Modelo Ticket

**Mostrar el código:**

```php
// app/Models/Ticket.php
class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Conceptos clave:**

-   Fillable vs Guarded
-   Relaciones Eloquent
-   Scopes y Accessors

#### 3.2 Relaciones

**Explicar:**

-   `belongsTo`: Un ticket pertenece a un usuario
-   `hasMany`: Un usuario tiene muchos tickets
-   Eager Loading para evitar N+1

### Parte 4: Controlador (45 minutos)

#### 4.1 Resource Controller

**Mostrar los 7 métodos:**

```php
// app/Http/Controllers/TicketController.php
class TicketController extends Controller
{
    public function index()    // Listar
    public function create()   // Formulario crear
    public function store()    // Guardar
    public function show()     // Mostrar
    public function edit()     // Formulario editar
    public function update()   // Actualizar
    public function destroy()  // Eliminar
}
```

#### 4.2 Método Index con Filtros

**Código detallado:**

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

#### 4.3 Validación

**Mostrar validación completa:**

```php
$validated = $request->validate([
    'user_id' => 'required|exists:users,id',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'status' => 'in:open,in_progress,closed',
], [
    'user_id.required' => 'El usuario es obligatorio',
    'title.required' => 'El título es obligatorio',
    // ... más mensajes
]);
```

### Parte 5: Vistas (45 minutos)

#### 5.1 Layout Principal

**Mostrar estructura:**

```php
// resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <link href="bootstrap.css">
</head>
<body>
    <nav>...</nav>
    <main>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
```

#### 5.2 Vista Index

**Mostrar características:**

-   Filtros de búsqueda
-   Tabla responsive
-   Badges de estado
-   Botones de acción
-   Paginación

#### 5.3 Formularios

**Mostrar validación en frontend:**

```php
<input type="text"
       name="title"
       class="form-control @error('title') is-invalid @enderror"
       value="{{ old('title') }}"
       required>
@error('title')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

### Parte 6: Rutas (15 minutos)

#### 6.1 Resource Routes

```php
// routes/web.php
Route::resource('tickets', TicketController::class);
```

**Mostrar todas las rutas generadas:**

```bash
php artisan route:list
```

### Parte 7: Datos de Prueba (15 minutos)

#### 7.1 Seeders

**Mostrar cómo crear datos:**

```php
// database/seeders/TicketSeeder.php
$tickets = [
    [
        'user_id' => $users->random()->id,
        'title' => 'Problema con el acceso',
        'description' => 'No puedo acceder al sistema...',
        'status' => 'open',
    ],
    // ... más tickets
];
```

---

## 🎯 Ejercicios Prácticos

### Ejercicio 1: Agregar Campo Prioridad (30 minutos)

**Objetivo:** Agregar un campo "prioridad" a los tickets

**Pasos:**

1. Crear migración para agregar columna
2. Actualizar modelo Ticket
3. Modificar controlador
4. Actualizar vistas

**Código de migración:**

```bash
php artisan make:migration add_priority_to_tickets_table
```

```php
public function up()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    });
}
```

### Ejercicio 2: Crear Vista de Estadísticas (45 minutos)

**Objetivo:** Mostrar estadísticas de tickets

**Pasos:**

1. Crear método en controlador
2. Crear vista con gráficos
3. Agregar ruta
4. Mostrar en navegación

### Ejercicio 3: Implementar Búsqueda Avanzada (60 minutos)

**Objetivo:** Búsqueda por múltiples criterios

**Pasos:**

1. Modificar formulario de búsqueda
2. Actualizar método index
3. Agregar filtros por fecha
4. Implementar ordenamiento

---

## ❓ Preguntas para Discusión

### Durante la Clase:

1. **¿Por qué usar Resource Controllers?**

    - Convención sobre configuración
    - Código más limpio
    - Rutas automáticas

2. **¿Cuándo usar Eager Loading?**

    - Problema N+1
    - Rendimiento
    - Cuándo cargar relaciones

3. **¿Cómo manejar la validación?**

    - Frontend vs Backend
    - Mensajes personalizados
    - Reglas de validación

4. **¿Qué ventajas tiene Blade?**
    - Sintaxis simple
    - Herencia de templates
    - Integración con Laravel

### Preguntas de Repaso:

1. ¿Qué es el patrón MVC?
2. ¿Cómo funcionan las relaciones en Eloquent?
3. ¿Qué es un Resource Controller?
4. ¿Cómo funciona la validación en Laravel?
5. ¿Qué son los seeders y para qué sirven?

---

## 🛠️ Solución de Problemas Comunes

### Error 1: "Class not found"

```bash
composer dump-autoload
```

### Error 2: "Table doesn't exist"

```bash
php artisan migrate
```

### Error 3: "Route not found"

```bash
php artisan route:clear
php artisan route:cache
```

### Error 4: "View not found"

-   Verificar que el archivo existe
-   Verificar la ruta del archivo
-   Limpiar cache de vistas: `php artisan view:clear`

---

## 📊 Evaluación

### Criterios de Evaluación:

1. **Comprensión de MVC** (25%)
2. **Implementación de CRUD** (30%)
3. **Uso de relaciones** (20%)
4. **Validación de datos** (15%)
5. **Interfaz de usuario** (10%)

### Actividades de Evaluación:

1. **Examen práctico:** Implementar una funcionalidad similar
2. **Presentación:** Explicar una parte del código
3. **Debugging:** Identificar y corregir errores
4. **Mejoras:** Proponer mejoras al sistema

---

## 🎉 Cierre de la Clase

### Resumen de lo Aprendido:

1. **Arquitectura MVC** en Laravel
2. **CRUD completo** con Resource Controllers
3. **Relaciones** entre modelos
4. **Validación** de formularios
5. **Vistas Blade** con Bootstrap
6. **Seeders** para datos de prueba

### Próximos Pasos:

1. Implementar autenticación
2. Agregar roles y permisos
3. Crear API REST
4. Implementar notificaciones
5. Agregar tests unitarios

### Recursos Adicionales:

-   [Documentación oficial de Laravel](https://laravel.com/docs)
-   [Laracasts](https://laracasts.com)
-   [Laravel News](https://laravel-news.com)
-   [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

## 📝 Notas para el Instructor

### Consejos para la Clase:

1. **Mostrar el código funcionando** desde el principio
2. **Explicar el "por qué"** además del "cómo"
3. **Usar ejemplos reales** y casos de uso
4. **Fomentar preguntas** y participación
5. **Dar tiempo para ejercicios** prácticos

### Puntos de Atención:

1. **Velocidad:** Ajustar según el nivel de los estudiantes
2. **Preguntas:** Responder todas las dudas
3. **Código:** Mostrar el código completo
4. **Práctica:** Dar tiempo para experimentar
5. **Feedback:** Recoger comentarios de los estudiantes

### Materiales de Apoyo:

-   Código comentado
-   Diagramas de arquitectura
-   Ejemplos de casos de uso
-   Lista de comandos útiles
-   Recursos de aprendizaje
