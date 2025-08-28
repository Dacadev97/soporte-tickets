<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;

/**
 * Controlador para gestionar las operaciones CRUD de tickets de soporte
 * 
 * Este controlador maneja todas las operaciones relacionadas con los tickets:
 * - Listar tickets con filtros y búsqueda
 * - Crear nuevos tickets
 * - Mostrar detalles de un ticket
 * - Editar tickets existentes
 * - Eliminar tickets
 * 
 * @package App\Http\Controllers
 */
class TicketController extends Controller
{
    /**
     * Muestra la lista de todos los tickets con funcionalidades de filtrado y búsqueda
     * 
     * Esta función permite:
     * - Ver todos los tickets ordenados por fecha de creación (más recientes primero)
     * - Filtrar tickets por estado (abierto, en progreso, cerrado)
     * - Buscar tickets por título usando búsqueda parcial
     * - Paginación automática (10 tickets por página)
     * - Carga eager loading de la relación con usuarios para optimizar consultas
     * 
     * @return \Illuminate\View\View Vista que muestra la lista de tickets
     */
    public function index()
    {
        // Inicializa la consulta con relación de usuario cargada (eager loading)
        $query = Ticket::with('user');

        // Aplica filtro por estado si se proporciona en la URL
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Aplica búsqueda por título si se proporciona en la URL
        // Usa LIKE para búsqueda parcial (encuentra coincidencias en cualquier parte del título)
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        // Ordena por fecha de creación descendente y aplica paginación
        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        // Retorna la vista con los tickets paginados
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Muestra el formulario para crear un nuevo ticket
     * 
     * Esta función:
     * - Obtiene la lista de todos los usuarios disponibles para asignar al ticket
     * - Retorna la vista del formulario de creación con los usuarios
     * - Los usuarios se pasan a la vista para mostrar en un dropdown de selección
     * 
     * @return \Illuminate\View\View Vista del formulario de creación de ticket
     */
    public function create()
    {
        // Obtiene todos los usuarios para el dropdown de selección
        $users = User::all();

        // Retorna la vista de creación con los usuarios disponibles
        return view('tickets.create', compact('users'));
    }

    /**
     * Almacena un nuevo ticket en la base de datos
     * 
     * Esta función:
     * - Valida los datos del formulario antes de crear el ticket
     * - Crea un nuevo registro de ticket en la base de datos
     * - Redirige al usuario a la lista de tickets con mensaje de éxito
     * - Incluye validaciones completas con mensajes personalizados en español
     * 
     * Validaciones aplicadas:
     * - user_id: Debe existir y ser obligatorio
     * - title: Debe ser obligatorio, string y máximo 255 caracteres
     * - description: Debe ser obligatorio y string
     * - status: Debe ser uno de los valores permitidos (open, in_progress, closed)
     * 
     * @param Request $request Datos del formulario enviados por el usuario
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario con reglas específicas
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'in:open,in_progress,closed',
        ], [
            // Mensajes de error personalizados en español
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'status.in' => 'El estado debe ser: abierto, en progreso o cerrado',
        ]);

        // Crea el nuevo ticket con los datos validados
        $ticket = Ticket::create($validated);

        // Redirige a la lista de tickets con mensaje de éxito
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket creado exitosamente');
    }

    /**
     * Muestra los detalles de un ticket específico
     * 
     * Esta función:
     * - Busca el ticket por su ID en la base de datos
     * - Carga la relación con el usuario asociado (eager loading)
     * - Si el ticket no existe, lanza una excepción 404 automáticamente
     * - Retorna la vista de detalles con toda la información del ticket
     * 
     * @param string $id ID del ticket a mostrar
     * @return \Illuminate\View\View Vista con los detalles del ticket
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el ticket no existe
     */
    public function show(string $id)
    {
        // Busca el ticket con su usuario asociado, lanza 404 si no existe
        $ticket = Ticket::with('user')->findOrFail($id);

        // Retorna la vista de detalles con el ticket
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Muestra el formulario para editar un ticket existente
     * 
     * Esta función:
     * - Busca el ticket por su ID en la base de datos
     * - Obtiene la lista de todos los usuarios para el dropdown de selección
     * - Si el ticket no existe, lanza una excepción 404 automáticamente
     * - Retorna la vista del formulario de edición con los datos del ticket y usuarios
     * 
     * @param string $id ID del ticket a editar
     * @return \Illuminate\View\View Vista del formulario de edición
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el ticket no existe
     */
    public function edit(string $id)
    {
        // Busca el ticket por ID, lanza 404 si no existe
        $ticket = Ticket::findOrFail($id);

        // Obtiene todos los usuarios para el dropdown de selección
        $users = User::all();

        // Retorna la vista de edición con el ticket y usuarios disponibles
        return view('tickets.edit', compact('ticket', 'users'));
    }

    /**
     * Actualiza un ticket existente en la base de datos
     * 
     * Esta función:
     * - Busca el ticket por su ID en la base de datos
     * - Valida los datos del formulario antes de actualizar
     * - Actualiza el ticket con los nuevos datos validados
     * - Redirige al usuario a la lista de tickets con mensaje de éxito
     * - Si el ticket no existe, lanza una excepción 404 automáticamente
     * 
     * Validaciones aplicadas (iguales que en store):
     * - user_id: Debe existir y ser obligatorio
     * - title: Debe ser obligatorio, string y máximo 255 caracteres
     * - description: Debe ser obligatorio y string
     * - status: Debe ser uno de los valores permitidos (open, in_progress, closed)
     * 
     * @param Request $request Datos del formulario enviados por el usuario
     * @param string $id ID del ticket a actualizar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el ticket no existe
     */
    public function update(Request $request, string $id)
    {
        // Busca el ticket por ID, lanza 404 si no existe
        $ticket = Ticket::findOrFail($id);

        // Valida los datos del formulario con las mismas reglas que en store
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'in:open,in_progress,closed',
        ], [
            // Mensajes de error personalizados en español
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'status.in' => 'El estado debe ser: abierto, en progreso o cerrado',
        ]);

        // Actualiza el ticket con los datos validados
        $ticket->update($validated);

        // Redirige a la lista de tickets con mensaje de éxito
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket actualizado exitosamente');
    }

    /**
     * Elimina un ticket de la base de datos
     * 
     * Esta función:
     * - Busca el ticket por su ID en la base de datos
     * - Elimina permanentemente el ticket de la base de datos
     * - Redirige al usuario a la lista de tickets con mensaje de éxito
     * - Si el ticket no existe, lanza una excepción 404 automáticamente
     * 
     * Nota: Esta operación es irreversible. Una vez eliminado, el ticket
     * no se puede recuperar a menos que se implemente soft deletes.
     * 
     * @param string $id ID del ticket a eliminar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el ticket no existe
     */
    public function destroy(string $id)
    {
        // Busca el ticket por ID, lanza 404 si no existe
        $ticket = Ticket::findOrFail($id);

        // Elimina permanentemente el ticket de la base de datos
        $ticket->delete();

        // Redirige a la lista de tickets con mensaje de éxito
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado exitosamente');
    }
}
