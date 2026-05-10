<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CandidatService;
use App\Models\Candidat;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $candidatService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->candidatService = new CandidatService(new Candidat());
    }

    /**
     * Test assigning a candidate to a session.
     */
    public function test_assign_to_session_updates_record()
    {
        // Mansouri (ID 3) is currently in Session 2 in CSV
        $this->candidatService->assignToSession(3, 1);
        
        $this->assertEquals(1, Candidat::find(3)->session_id);
    }

    /**
     * Test getting unassigned candidates.
     */
    public function test_get_unassigned_returns_candidates_without_tickets()
    {
        // Add a candidate without a ticket
        Candidat::create([
            'nom' => 'Sans',
            'prenom' => 'Ticket',
            'cin' => 'TEST000',
            'scoreQCM' => 14,
            'session_id' => 1
        ]);

        $unassigned = $this->candidatService->getUnassigned();
        
        $this->assertCount(1, $unassigned);
        $this->assertEquals('Sans', $unassigned->first()->nom);
    }

    /**
     * Test getting recent activity logs.
     */
    public function test_get_recent_activity_returns_seeded_tickets()
    {
        $activity = $this->candidatService->getRecentActivity(5);
        
        // CSV has 3 tickets seeded
        $this->assertCount(3, $activity);
    }
}
