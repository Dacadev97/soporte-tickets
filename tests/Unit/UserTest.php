<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Pruebas unitarias para el modelo User
 * 
 * Estas pruebas verifican:
 * - Creación y asignación de atributos
 * - Relaciones con otros modelos
 * - Funcionalidades de autenticación
 * - Validaciones de campos
 * - Encriptación de contraseñas
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que se puede crear un usuario con todos los campos requeridos
     */
    public function test_can_create_user_with_required_fields()
    {
        $user = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com',
            'password' => Hash::make('password123')
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Juan Pérez', $user->name);
        $this->assertEquals('juan@ejemplo.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Prueba que se pueden crear tickets asociados a un usuario
     */
    public function test_can_create_tickets_for_user()
    {
        $user = User::factory()->create();
        
        // Crear varios tickets para el usuario
        $ticket1 = Ticket::factory()->create(['user_id' => $user->id]);
        $ticket2 = Ticket::factory()->create(['user_id' => $user->id]);
        $ticket3 = Ticket::factory()->create(['user_id' => $user->id]);

        // Verificar que los tickets se crearon correctamente
        $this->assertDatabaseHas('tickets', ['id' => $ticket1->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('tickets', ['id' => $ticket2->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('tickets', ['id' => $ticket3->id, 'user_id' => $user->id]);
    }

    /**
     * Prueba que los campos fillable están correctamente definidos
     */
    public function test_fillable_attributes_are_correctly_defined()
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password', 'role'];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /**
     * Prueba que los campos hidden están correctamente definidos
     */
    public function test_hidden_attributes_are_correctly_defined()
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];
        
        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /**
     * Prueba que los campos cast están correctamente definidos
     */
    public function test_casts_are_correctly_defined()
    {
        $user = new User();
        $casts = $user->getCasts();
        
        // Verificar que los casts esperados están presentes
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }

    /**
     * Prueba que se puede actualizar un usuario existente
     */
    public function test_can_update_existing_user()
    {
        $user = User::factory()->create([
            'name' => 'Nombre Original',
            'email' => 'original@ejemplo.com'
        ]);

        // Actualizar el usuario
        $user->update([
            'name' => 'Nombre Actualizado',
            'email' => 'actualizado@ejemplo.com'
        ]);

        // Verificar que se actualizó correctamente
        $this->assertEquals('Nombre Actualizado', $user->fresh()->name);
        $this->assertEquals('actualizado@ejemplo.com', $user->fresh()->email);
    }

    /**
     * Prueba que se puede eliminar un usuario
     */
    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        // Verificar que el usuario existe
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // Eliminar el usuario
        $user->delete();

        // Verificar que el usuario fue eliminado
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Prueba que el email debe ser único
     */
    public function test_email_must_be_unique()
    {
        // Crear un usuario con un email
        User::factory()->create(['email' => 'test@ejemplo.com']);

        // Intentar crear otro usuario con el mismo email
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Otro Usuario',
            'email' => 'test@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);
    }

    /**
     * Prueba que se pueden obtener tickets de un usuario por estado usando consultas directas
     */
    public function test_can_get_user_tickets_by_status()
    {
        $user = User::factory()->create();
        
        // Crear tickets con diferentes estados
        $openTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $inProgressTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'in_progress']);
        $closedTicket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        // Obtener tickets abiertos del usuario usando consulta directa
        $openTickets = Ticket::where('user_id', $user->id)->where('status', 'open')->get();
        $this->assertCount(1, $openTickets);
        $this->assertEquals($openTicket->id, $openTickets->first()->id);

        // Obtener tickets en progreso del usuario
        $inProgressTickets = Ticket::where('user_id', $user->id)->where('status', 'in_progress')->get();
        $this->assertCount(1, $inProgressTickets);
        $this->assertEquals($inProgressTicket->id, $inProgressTickets->first()->id);

        // Obtener tickets cerrados del usuario
        $closedTickets = Ticket::where('user_id', $user->id)->where('status', 'closed')->get();
        $this->assertCount(1, $closedTickets);
        $this->assertEquals($closedTicket->id, $closedTickets->first()->id);
    }

    /**
     * Prueba que se puede contar los tickets de un usuario
     */
    public function test_can_count_user_tickets()
    {
        $user = User::factory()->create();
        
        // Crear varios tickets para el usuario
        Ticket::factory()->count(5)->create(['user_id' => $user->id]);

        // Verificar que el conteo es correcto usando consulta directa
        $this->assertEquals(5, Ticket::where('user_id', $user->id)->count());
    }

    /**
     * Prueba que se puede obtener el usuario más reciente
     */
    public function test_can_get_latest_user()
    {
        // Crear usuarios en diferentes momentos
        $firstUser = User::factory()->create();
        sleep(1); // Pequeña pausa para asegurar diferentes timestamps
        $secondUser = User::factory()->create();

        // Obtener el usuario más reciente
        $latestUser = User::latest()->first();

        $this->assertEquals($secondUser->id, $latestUser->id);
    }
}
