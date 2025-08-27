# Sistema de Tickets de Soporte

Un sistema completo de gestión de tickets de soporte desarrollado con Laravel.

## Características

-   ✅ CRUD completo de tickets
-   ✅ Gestión de usuarios
-   ✅ Estados de tickets (Abierto, En Progreso, Cerrado)
-   ✅ Búsqueda y filtrado de tickets
-   ✅ Interfaz moderna con Bootstrap 5
-   ✅ Validación de formularios
-   ✅ Paginación
-   ✅ Mensajes de confirmación

## Requisitos

-   PHP 8.1 o superior
-   Composer
-   MySQL/PostgreSQL/SQLite
-   Node.js (opcional, para compilar assets)

## Instalación

1. **Clonar el repositorio**

    ```bash
    git clone <url-del-repositorio>
    cd soporte-tickets
    ```

2. **Instalar dependencias**

    ```bash
    composer install
    ```

3. **Configurar el archivo .env**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configurar la base de datos**
   Edita el archivo `.env` y configura tu base de datos:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=soporte_tickets
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_password
    ```

5. **Ejecutar las migraciones**

    ```bash
    php artisan migrate
    ```

6. **Poblar la base de datos con datos de prueba**

    ```bash
    php artisan db:seed
    ```

7. **Iniciar el servidor de desarrollo**

    ```bash
    php artisan serve
    ```

8. **Acceder a la aplicación**
   Abre tu navegador y ve a `http://localhost:8000`

## Estructura del Proyecto

```
app/
├── Http/Controllers/
│   └── TicketController.php    # Controlador principal de tickets
├── Models/
│   ├── Ticket.php             # Modelo de tickets
│   └── User.php               # Modelo de usuarios
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php      # Layout principal
    └── tickets/
        ├── index.blade.php    # Lista de tickets
        ├── create.blade.php   # Crear ticket
        ├── show.blade.php     # Ver ticket
        └── edit.blade.php     # Editar ticket
database/
├── migrations/
│   └── 2025_08_26_233902_tickets.php
└── seeders/
    ├── UserSeeder.php         # Datos de prueba de usuarios
    └── TicketSeeder.php       # Datos de prueba de tickets
```

## Funcionalidades

### Gestión de Tickets

-   **Listar tickets**: Vista con tabla paginada y filtros
-   **Crear ticket**: Formulario con validación
-   **Ver ticket**: Vista detallada con información completa
-   **Editar ticket**: Formulario de edición
-   **Eliminar ticket**: Confirmación antes de eliminar

### Filtros y Búsqueda

-   Filtro por estado (Abierto, En Progreso, Cerrado)
-   Búsqueda por título
-   Ordenamiento por fecha de creación

### Estados de Tickets

-   **Abierto**: Ticket recién creado
-   **En Progreso**: Ticket siendo atendido
-   **Cerrado**: Ticket resuelto

## Rutas Disponibles

| Método | Ruta                 | Descripción                    |
| ------ | -------------------- | ------------------------------ |
| GET    | `/`                  | Redirige a la lista de tickets |
| GET    | `/tickets`           | Lista de tickets               |
| GET    | `/tickets/create`    | Formulario de creación         |
| POST   | `/tickets`           | Crear ticket                   |
| GET    | `/tickets/{id}`      | Ver ticket                     |
| GET    | `/tickets/{id}/edit` | Formulario de edición          |
| PUT    | `/tickets/{id}`      | Actualizar ticket              |
| DELETE | `/tickets/{id}`      | Eliminar ticket                |

## Datos de Prueba

El sistema incluye datos de prueba:

-   **5 usuarios** con diferentes nombres y emails
-   **8 tickets** con diferentes estados y descripciones

## Tecnologías Utilizadas

-   **Backend**: Laravel 10
-   **Frontend**: Bootstrap 5, Font Awesome
-   **Base de datos**: MySQL/PostgreSQL/SQLite
-   **Validación**: Laravel Validation
-   **Paginación**: Laravel Pagination

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## Soporte

Si tienes alguna pregunta o problema, por favor abre un issue en el repositorio.
