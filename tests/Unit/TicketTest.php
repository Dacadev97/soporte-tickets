<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas unitarias para el modelo Ticket
 * 
 * Estas pruebas verifican:
 * - Creación y asignación de atributos
 * - Relaciones con otros modelos
 * - Scopes para filtrar por estado
 * - Accessors para formatear datos
 * - Validaciones de campos
 */
class TicketTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que se puede crear un ticket con todos los campos requeridos
     */
    public function test_can_create_ticket_with_required_fields()
    {
        // Crear un usuario para asociar al ticket
        $user = User::factory()->create();

        // Crear un ticket con datos válidos
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'title' => 'Problema con el sistema',
            'description' => 'El sistema no responde correctamente',
            'status' => 'open'
        ]);

        // Verificar que el ticket se creó correctamente
        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals($user->id, $ticket->user_id);
        $this->assertEquals('Problema con el sistema', $ticket->title);
        $this->assertEquals('El sistema no responde correctamente', $ticket->description);
        $this->assertEquals('open', $ticket->status);
    }

    /**
     * Prueba que el ticket tiene una relación con User
     */
    public function test_ticket_belongs_to_user()
    {
        // Crear un usuario y un ticket asociado
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        // Verificar la relación
        $this->assertInstanceOf(User::class, $ticket->user);
        $this->assertEquals($user->id, $ticket->user->id);
        $this->assertEquals($user->name, $ticket->user->name);
    }

    /**
     * Prueba el scope byStatus para filtrar tickets por estado
     */
    public function test_scope_by_status_filters_tickets_correctly()
    {
        // Crear tickets con diferentes estados
        $user = User::factory()->create();
        $openTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $inProgressTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        $closedTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        // Probar filtro por estado 'open'
        $openTickets = Ticket::byStatus('open')->get();
        $this->assertCount(1, $openTickets);
        $this->assertEquals($openTicket->id, $openTickets->first()->id);

        // Probar filtro por estado 'in_progress'
        $inProgressTickets = Ticket::byStatus('in_progress')->get();
        $this->assertCount(1, $inProgressTickets);
        $this->assertEquals($inProgressTicket->id, $inProgressTickets->first()->id);

        // Probar filtro por estado 'closed'
        $closedTickets = Ticket::byStatus('closed')->get();
        $this->assertCount(1, $closedTickets);
        $this->assertEquals($closedTicket->id, $closedTickets->first()->id);
    }

    /**
     * Prueba el scope open para obtener solo tickets abiertos
     */
    public function test_scope_open_returns_only_open_tickets()
    {
        // Crear tickets con diferentes estados
        $user = User::factory()->create();
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        // Obtener solo tickets abiertos
        $openTickets = Ticket::open()->get();

        // Verificar que solo hay tickets abiertos
        $this->assertCount(1, $openTickets);
        $this->assertEquals('open', $openTickets->first()->status);
    }

    /**
     * Prueba el scope inProgress para obtener solo tickets en progreso
     */
    public function test_scope_in_progress_returns_only_in_progress_tickets()
    {
        // Crear tickets con diferentes estados
        $user = User::factory()->create();
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        // Obtener solo tickets en progreso
        $inProgressTickets = Ticket::inProgress()->get();

        // Verificar que solo hay tickets en progreso
        $this->assertCount(1, $inProgressTickets);
        $this->assertEquals('in_progress', $inProgressTickets->first()->status);
    }

    /**
     * Prueba el scope closed para obtener solo tickets cerrados
     */
    public function test_scope_closed_returns_only_closed_tickets()
    {
        // Crear tickets con diferentes estados
        $user = User::factory()->create();
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        // Obtener solo tickets cerrados
        $closedTickets = Ticket::closed()->get();

        // Verificar que solo hay tickets cerrados
        $this->assertCount(1, $closedTickets);
        $this->assertEquals('closed', $closedTickets->first()->status);
    }

    /**
     * Prueba el accessor getStatusTextAttribute para obtener el estado en español
     */
    public function test_status_text_attribute_returns_spanish_status()
    {
        $user = User::factory()->create();

        // Probar estado 'open'
        $openTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $this->assertEquals('Abierto', $openTicket->status_text);

        // Probar estado 'in_progress'
        $inProgressTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        $this->assertEquals('En Progreso', $inProgressTicket->status_text);

        // Probar estado 'closed'
        $closedTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);
        $this->assertEquals('Cerrado', $closedTicket->status_text);
    }

    /**
     * Prueba que los campos fillable están correctamente definidos
     */
    public function test_fillable_attributes_are_correctly_defined()
    {
        $ticket = new Ticket();
        $expectedFillable = ['user_id', 'title', 'description', 'status'];
        
        $this->assertEquals($expectedFillable, $ticket->getFillable());
    }

    /**
     * Prueba que se puede actualizar un ticket existente
     */
    public function test_can_update_existing_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'title' => 'Título original',
            'description' => 'Descripción original',
            'status' => 'open'
        ]);

        // Actualizar el ticket
        $ticket->update([
            'title' => 'Título actualizado',
            'description' => 'Descripción actualizada',
            'status' => 'in_progress'
        ]);

        // Verificar que se actualizó correctamente
        $this->assertEquals('Título actualizado', $ticket->fresh()->title);
        $this->assertEquals('Descripción actualizada', $ticket->fresh()->description);
        $this->assertEquals('in_progress', $ticket->fresh()->status);
    }

    /**
     * Prueba que se puede eliminar un ticket
     */
    public function test_can_delete_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        // Verificar que el ticket existe
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);

        // Eliminar el ticket
        $ticket->delete();

        // Verificar que el ticket fue eliminado
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }
}
