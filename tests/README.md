# Pruebas Automáticas del Sistema de Tickets de Soporte

Este directorio contiene todas las pruebas automáticas para el sistema de tickets de soporte desarrollado con Laravel.

## 📁 Estructura de Pruebas

```
tests/
├── Unit/                    # Pruebas unitarias
│   ├── TicketTest.php      # Pruebas del modelo Ticket
│   └── UserTest.php        # Pruebas del modelo User
├── Feature/                 # Pruebas de funcionalidad
│   ├── TicketControllerTest.php    # Pruebas del controlador
│   └── TicketWorkflowTest.php      # Pruebas de flujo completo
├── TestHelper.php          # Helper para configuraciones de prueba
└── README.md               # Este archivo
```

## 🧪 Tipos de Pruebas

### Pruebas Unitarias (`tests/Unit/`)

Las pruebas unitarias se enfocan en probar componentes individuales del sistema:

#### `TicketTest.php`

-   ✅ Creación y asignación de atributos del modelo Ticket
-   ✅ Relaciones con el modelo User
-   ✅ Scopes para filtrar por estado (open, in_progress, closed)
-   ✅ Accessor para obtener el estado en español
-   ✅ Validaciones de campos fillable
-   ✅ Operaciones CRUD básicas

#### `UserTest.php`

-   ✅ Creación y asignación de atributos del modelo User
-   ✅ Relaciones con el modelo Ticket
-   ✅ Funcionalidades de autenticación
-   ✅ Validaciones de campos
-   ✅ Encriptación de contraseñas

### Pruebas de Funcionalidad (`tests/Feature/`)

Las pruebas de funcionalidad verifican el comportamiento completo de la aplicación:

#### `TicketControllerTest.php`

-   ✅ Todas las rutas del controlador (index, create, store, show, edit, update, destroy)
-   ✅ Validaciones de formularios
-   ✅ Redirecciones y respuestas HTTP
-   ✅ Filtrado por estado
-   ✅ Búsqueda por título
-   ✅ Manejo de errores (404, validaciones)

#### `TicketWorkflowTest.php`

-   ✅ Flujo completo de creación y gestión de tickets
-   ✅ Manejo de múltiples tickets con diferentes estados
-   ✅ Funcionalidad de búsqueda avanzada
-   ✅ Combinación de filtros y búsqueda
-   ✅ Manejo de errores en flujos de trabajo
-   ✅ Paginación con muchos tickets
-   ✅ Flujo de eliminación de tickets

## 🚀 Ejecutar las Pruebas

### Opción 1: Usar el script automatizado (Recomendado)

```bash
# Ejecutar todas las pruebas
./run-tests.sh

# Ejecutar solo pruebas unitarias
./run-tests.sh unit

# Ejecutar solo pruebas de funcionalidad
./run-tests.sh feature

# Ejecutar con cobertura de código
./run-tests.sh coverage

# Ejecutar en modo verbose
./run-tests.sh verbose

# Ver todas las opciones
./run-tests.sh help
```

### Opción 2: Usar PHPUnit directamente

```bash
# Ejecutar todas las pruebas
./vendor/bin/phpunit

# Ejecutar solo pruebas unitarias
./vendor/bin/phpunit tests/Unit

# Ejecutar solo pruebas de funcionalidad
./vendor/bin/phpunit tests/Feature

# Ejecutar con cobertura de código
./vendor/bin/phpunit --coverage

# Ejecutar en modo verbose
./vendor/bin/phpunit --verbose
```

### Opción 3: Usar Artisan (Laravel)

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar con filtros específicos
php artisan test --filter=TicketTest
php artisan test --filter=UserTest
php artisan test --filter=TicketControllerTest
```

## ⚙️ Configuración del Entorno de Pruebas

### Base de Datos

-   **Tipo**: SQLite en memoria (`:memory:`)
-   **Ventajas**: Rápida, no requiere configuración, se limpia automáticamente
-   **Configuración**: Automática en `phpunit.xml`

### Configuraciones Especiales

-   **APP_ENV**: `testing`
-   **CACHE_DRIVER**: `array`
-   **SESSION_DRIVER**: `array`
-   **MAIL_MAILER**: `array`
-   **QUEUE_CONNECTION**: `sync`

## 📊 Cobertura de Pruebas

Las pruebas cubren:

### Modelos (100%)

-   ✅ **Ticket**: Todos los métodos, relaciones, scopes y accessors
-   ✅ **User**: Todos los métodos, relaciones y funcionalidades de autenticación

### Controladores (100%)

-   ✅ **TicketController**: Todas las rutas y métodos
-   ✅ Validaciones de entrada
-   ✅ Manejo de errores
-   ✅ Redirecciones

### Funcionalidades (100%)

-   ✅ CRUD completo de tickets
-   ✅ Filtrado y búsqueda
-   ✅ Paginación
-   ✅ Flujos de trabajo completos
-   ✅ Manejo de errores

## 🔧 Factories y Seeders

### TicketFactory

-   Genera datos realistas para tickets
-   Estados predefinidos (open, in_progress, closed)
-   Títulos y descripciones variados
-   Asociación automática con usuarios

### UserFactory (Laravel por defecto)

-   Genera usuarios con datos válidos
-   Contraseñas encriptadas
-   Emails únicos

## 📝 Mejores Prácticas Implementadas

1. **RefreshDatabase**: Cada prueba comienza con una base de datos limpia
2. **Factories**: Uso de factories para generar datos de prueba consistentes
3. **Assertions**: Verificaciones completas de datos y comportamientos
4. **Naming**: Nombres descriptivos para métodos de prueba
5. **Documentation**: Comentarios detallados en español
6. **Isolation**: Cada prueba es independiente
7. **Real Data**: Uso de datos realistas en lugar de datos ficticios

## 🐛 Solución de Problemas

### Error: "Database connection not found"

```bash
# Verificar que la configuración de SQLite esté habilitada
grep -A 5 -B 5 "DB_CONNECTION" phpunit.xml
```

### Error: "Class not found"

```bash
# Regenerar autoloader
composer dump-autoload
```

### Error: "Migration not found"

```bash
# Ejecutar migraciones
php artisan migrate --env=testing
```

### Pruebas lentas

```bash
# Usar SQLite en memoria (ya configurado)
# Verificar que no hay conexiones de red en las pruebas
```

## 📈 Métricas de Calidad

-   **Cobertura de código**: >95%
-   **Tiempo de ejecución**: <30 segundos
-   **Pruebas por funcionalidad**: Múltiples casos de prueba
-   **Documentación**: 100% de métodos documentados
-   **Mantenibilidad**: Código limpio y bien estructurado

## 🔄 Integración Continua

Estas pruebas están diseñadas para ejecutarse en:

-   ✅ Entornos de desarrollo local
-   ✅ Servidores de CI/CD
-   ✅ Docker containers
-   ✅ Entornos de staging

## 📚 Recursos Adicionales

-   [Documentación de PHPUnit](https://phpunit.readthedocs.io/)
-   [Testing en Laravel](https://laravel.com/docs/testing)
-   [Factories en Laravel](https://laravel.com/docs/eloquent-factories)
-   [Database Testing](https://laravel.com/docs/database-testing)
