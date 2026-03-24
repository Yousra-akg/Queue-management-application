<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TicketService;
use App\Models\Ticket;
use App\Models\Candidat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $ticketService;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database with CSV data
        $this->seed();
        
        $this->ticketService = new TicketService(new Ticket());
    }

    /**
     * Test getting the live queue.
     */
    public function test_get_live_queue_returns_correct_count()
    {
        // Session Alpha (ID 1) has 2 tickets in CSV: 1 "terminée", 1 "en cours"
        // getLiveQueue should return only "en attente" and "en cours"
        $queue = $this->ticketService->getLiveQueue(1);
        
        $this->assertCount(1, $queue);
        $this->assertEquals('Benani', $queue->first()->candidat->nom);
    }

    /**
     * Test presence validation logic.
     */
    public function test_validate_presence_with_correct_code()
    {
        // Mansouri (Ticket ID 3) belongs to Session Beta (ID 2)
        // Session Beta code is "C34D"
        $ticket = Ticket::find(3);
        $this->assertEquals('en attente', $ticket->statut);

        $result = $this->ticketService->validatePresence(3, 'C34D');

        $this->assertTrue($result);
        $this->assertEquals('en cours', $ticket->fresh()->statut);
    }

    /**
     * Test ticket generation.
     */
    public function test_generate_ticket_creates_new_record()
    {
        // Add a new candidate manually for this test
        $candidat = Candidat::create([
            'nom' => 'Nouveau',
            'prenom' => 'Candidat',
            'scoreQCM' => 12.5,
            'session_id' => 1
        ]);

        $ticket = $this->ticketService->generateTicket($candidat->id);

        $this->assertNotNull($ticket);
        $this->assertEquals($candidat->id, $ticket->candidat_id);
        // Max order for session 1 in CSV was 2
        $this->assertEquals(3, $ticket->numeroOrdre);
    }
}
