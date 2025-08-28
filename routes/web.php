<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Rutas Web - Sistema de Tickets de Soporte
|--------------------------------------------------------------------------
|
| Aquí se definen todas las rutas web de la aplicación de tickets de soporte.
| Todas las rutas están cargadas por el RouteServiceProvider y se asignan
| al grupo de middleware "web".
|
| Rutas disponibles:
| - GET / → Redirige a la lista de tickets
| - GET /tickets → Lista todos los tickets (index)
| - GET /tickets/create → Formulario para crear ticket (create)
| - POST /tickets → Guarda nuevo ticket (store)
| - GET /tickets/{id} → Muestra detalles del ticket (show)
| - GET /tickets/{id}/edit → Formulario para editar ticket (edit)
| - PUT/PATCH /tickets/{id} → Actualiza ticket existente (update)
| - DELETE /tickets/{id} → Elimina ticket (destroy)
|
*/

/**
 * Ruta principal - Redirige automáticamente a la lista de tickets
 * 
 * Cuando un usuario accede a la raíz del sitio, se le redirige
 * automáticamente a la página de listado de tickets para una
 * mejor experiencia de usuario.
 */
Route::get('/', function () {
    return redirect()->route('tickets.index');
});

/**
 * Rutas de recursos para tickets
 * 
 * Crea automáticamente todas las rutas CRUD necesarias para el modelo Ticket:
 * - index: Lista todos los tickets
 * - create: Muestra formulario de creación
 * - store: Guarda nuevo ticket
 * - show: Muestra detalles de un ticket
 * - edit: Muestra formulario de edición
 * - update: Actualiza ticket existente
 * - destroy: Elimina ticket
 * 
 * Todas estas rutas están manejadas por el TicketController.
 */
Route::resource('tickets', TicketController::class);
