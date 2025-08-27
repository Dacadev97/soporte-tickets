# 🛠️ Comandos Artisan Útiles - Sistema de Tickets

## 📋 Comandos Básicos de Laravel

### 🔧 Crear Elementos del Proyecto

```bash
# Crear migración
php artisan make:migration create_tickets_table

# Crear migración para agregar columna
php artisan make:migration add_priority_to_tickets_table

# Crear modelo
php artisan make:model Ticket

# Crear modelo con migración
php artisan make:model Ticket -m

# Crear modelo con migración y seeder
php artisan make:model Ticket -ms

# Crear controlador
php artisan make:controller TicketController

# Crear controlador con métodos resource
php artisan make:controller TicketController --resource

# Crear seeder
php artisan make:seeder UserSeeder

# Crear seeder
php artisan make:seeder TicketSeeder

# Crear evento
php artisan make:event TicketCreated

# Crear listener
php artisan make:listener SendTicketNotification

# Crear middleware
php artisan make:middleware CheckTicketAccess

# Crear request personalizado
php artisan make:request StoreTicketRequest

# Crear exportación Excel
php artisan make:export TicketsExport
```

### 🗄️ Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Revertir todas las migraciones
php artisan migrate:reset

# Revertir y ejecutar (fresh)
php artisan migrate:fresh

# Revertir, ejecutar y poblar con datos
php artisan migrate:fresh --seed

# Ejecutar seeders específicos
php artisan db:seed --class=UserSeeder

# Ejecutar seeders específicos
php artisan db:seed --class=TicketSeeder

# Ver estado de migraciones
php artisan migrate:status

# Crear migración de rollback
php artisan make:migration rollback_tickets_table
```

### 🛣️ Rutas

```bash
# Listar todas las rutas
php artisan route:list

# Listar rutas específicas
php artisan route:list --name=tickets

# Limpiar cache de rutas
php artisan route:clear

# Cachear rutas (producción)
php artisan route:cache

# Ver rutas en formato JSON
php artisan route:list --json
```

### 🎨 Vistas

```bash
# Limpiar cache de vistas
php artisan view:clear

# Cachear vistas (producción)
php artisan view:cache

# Ver todas las vistas
php artisan view:list
```

### 🔍 Configuración

```bash
# Limpiar cache de configuración
php artisan config:clear

# Cachear configuración (producción)
php artisan config:cache

# Limpiar cache de aplicación
php artisan cache:clear

# Limpiar todos los caches
php artisan optimize:clear

# Recargar autoloader
composer dump-autoload

# Instalar dependencias
composer install

# Actualizar dependencias
composer update
```

### 🚀 Servidor

```bash
# Iniciar servidor de desarrollo
php artisan serve

# Iniciar servidor en puerto específico
php artisan serve --port=8080

# Iniciar servidor en host específico
php artisan serve --host=0.0.0.0

# Iniciar servidor con HTTPS
php artisan serve --host=0.0.0.0 --port=443 --tls-cert=server.crt --tls-key=server.key
```

### 🔑 Autenticación y Claves

```bash
# Generar clave de aplicación
php artisan key:generate

# Generar clave de aplicación específica
php artisan key:generate --show

# Crear usuario administrador
php artisan make:admin

# Crear token de API
php artisan make:token
```

## 📊 Comandos de Desarrollo

### 🐛 Debugging

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Limpiar logs
php artisan log:clear

# Ver configuración actual
php artisan config:show

# Ver variables de entorno
php artisan env

# Ver información del sistema
php artisan about
```

### 📈 Testing

```bash
# Ejecutar tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=TicketTest

# Ejecutar tests con coverage
php artisan test --coverage

# Crear test
php artisan make:test TicketTest

# Crear test de feature
php artisan make:test TicketTest --unit
```

### 🔧 Mantenimiento

```bash
# Poner aplicación en modo mantenimiento
php artisan down

# Poner aplicación en modo mantenimiento con mensaje
php artisan down --message="Actualizando sistema"

# Poner aplicación en modo mantenimiento con código secreto
php artisan down --secret=123456

# Levantar aplicación del modo mantenimiento
php artisan up

# Verificar estado de mantenimiento
php artisan down --render
```

## 🎯 Comandos Específicos del Proyecto

### 📋 Tickets

```bash
# Crear ticket desde consola
php artisan make:ticket --title="Problema de acceso" --user=1

# Listar tickets desde consola
php artisan tickets:list

# Exportar tickets a CSV
php artisan tickets:export --format=csv

# Importar tickets desde CSV
php artisan tickets:import --file=tickets.csv

# Generar reporte de tickets
php artisan tickets:report --month=2024-01
```

### 👥 Usuarios

```bash
# Crear usuario desde consola
php artisan make:user --name="Juan Pérez" --email="juan@example.com"

# Listar usuarios
php artisan users:list

# Cambiar contraseña de usuario
php artisan users:password --email=juan@example.com --password=nueva123
```

## 🔧 Comandos de Composer

```bash
# Instalar dependencias
composer install

# Instalar dependencia específica
composer require maatwebsite/excel

# Actualizar dependencias
composer update

# Actualizar dependencia específica
composer update laravel/framework

# Remover dependencia
composer remove package/name

# Ver dependencias instaladas
composer show

# Ver dependencias obsoletas
composer outdated

# Optimizar autoloader
composer dump-autoload --optimize

# Limpiar cache de Composer
composer clear-cache
```

## 🐳 Comandos de Docker (si usas Docker)

```bash
# Construir imagen
docker build -t laravel-app .

# Ejecutar contenedor
docker run -p 8000:8000 laravel-app

# Ejecutar con docker-compose
docker-compose up -d

# Ejecutar comandos en contenedor
docker-compose exec app php artisan migrate

# Ver logs del contenedor
docker-compose logs app

# Parar contenedores
docker-compose down
```

## 📱 Comandos de Node.js (si usas Vite)

```bash
# Instalar dependencias
npm install

# Ejecutar en desarrollo
npm run dev

# Compilar para producción
npm run build

# Ver dependencias
npm list

# Actualizar dependencias
npm update
```

## 🔍 Comandos de Git

```bash
# Inicializar repositorio
git init

# Agregar archivos
git add .

# Hacer commit
git commit -m "Implementar sistema de tickets"

# Ver estado
git status

# Ver historial
git log

# Crear rama
git checkout -b feature/priority-field

# Cambiar rama
git checkout main

# Fusionar rama
git merge feature/priority-field

# Ver diferencias
git diff
```

## 🎯 Comandos Útiles para la Clase

### 🔧 Configuración Inicial del Proyecto

```bash
# 1. Clonar proyecto (si es necesario)
git clone <url-del-repositorio>
cd soporte-tickets

# 2. Instalar dependencias
composer install

# 3. Copiar archivo de configuración
cp .env.example .env

# 4. Generar clave de aplicación
php artisan key:generate

# 5. Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=soporte_tickets
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 7. Iniciar servidor
php artisan serve
```

### 🐛 Solución de Problemas Comunes

```bash
# Error: Class not found
composer dump-autoload

# Error: Table doesn't exist
php artisan migrate

# Error: Route not found
php artisan route:clear
php artisan route:cache

# Error: View not found
php artisan view:clear

# Error: Configuration cache
php artisan config:clear
php artisan cache:clear

# Error: Permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 📊 Verificación del Sistema

```bash
# Verificar que todo funciona
php artisan about

# Verificar rutas
php artisan route:list

# Verificar configuración
php artisan config:show

# Verificar base de datos
php artisan migrate:status

# Verificar logs
tail -f storage/logs/laravel.log
```

## 📝 Notas para el Instructor

### Comandos Importantes para Mostrar en Clase:

1. **`php artisan make:controller TicketController --resource`**

    - Explicar qué métodos crea automáticamente

2. **`php artisan route:list`**

    - Mostrar todas las rutas generadas

3. **`php artisan migrate:fresh --seed`**

    - Explicar que elimina y recrea la base de datos

4. **`composer dump-autoload`**

    - Explicar el autoloader de Composer

5. **`php artisan serve`**
    - Mostrar cómo iniciar el servidor de desarrollo

### Consejos para la Clase:

-   **Mostrar los comandos en vivo** para que los estudiantes vean la salida
-   **Explicar qué hace cada comando** antes de ejecutarlo
-   **Usar ejemplos reales** con el proyecto de tickets
-   **Animar a los estudiantes** a experimentar con los comandos
-   **Proporcionar la lista** de comandos como referencia

### Comandos para Ejercicios Prácticos:

-   **Ejercicio 1:** `php artisan make:migration add_priority_to_tickets_table`
-   **Ejercicio 2:** `php artisan make:controller StatisticsController`
-   **Ejercicio 3:** `php artisan make:export TicketsExport`
-   **Ejercicio 4:** `php artisan make:event TicketCreated`

Esta lista de comandos será muy útil durante la clase para mostrar las capacidades de Laravel y Artisan.
