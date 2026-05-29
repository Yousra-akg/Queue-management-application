<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QueueService;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $queueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->queueService = app(QueueService::class);
    }

    /**
     * Test calling the next candidate in the queue.
     */
    public function test_call_next_candidat_updates_status()
    {
        // Session 2 has 1 ticket "en attente" (SOLI-03) in CSV
        $next = $this->queueService->callNextCandidat(2);
        
        $this->assertNotNull($next);
        $this->assertEquals('en cours', $next->statut);
    }

    /**
     * Test manual reordering of the queue.
     */
    public function test_reorder_queue_updates_orders()
    {
        // Reorder Session 1 tickets (IDs 1 and 2 in CSV)
        $this->queueService->reorderQueue(1, [2, 1]);
        
        $this->assertEquals(1, Ticket::find(2)->numeroOrdre);
        $this->assertEquals(2, Ticket::find(1)->numeroOrdre);
    }

    /**
     * Test manual status update.
     */
    public function test_update_candidat_status_works()
    {
        // Update ticket 3 (Mansouri) status
        $this->queueService->updateCandidatStatus(3, 'terminée');
        
        $this->assertEquals('terminée', Ticket::find(3)->statut);
    }
}
