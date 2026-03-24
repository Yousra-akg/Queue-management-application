<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SessionService;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $sessionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->sessionService = new SessionService(new Session());
    }

    /**
     * Test searching and filtering sessions.
     */
    public function test_search_and_filter_finds_session_by_name()
    {
        $results = $this->sessionService->searchAndFilter('Alpha');
        
        $this->assertCount(1, $results);
        $this->assertEquals('Session Alpha', $results->first()->nom);
    }

    /**
     * Test filtering by status.
     */
    public function test_search_and_filter_by_status()
    {
        $results = $this->sessionService->searchAndFilter('', 'en cours');
        
        $this->assertCount(1, $results);
        $this->assertEquals('Session Beta', $results->first()->nom);
    }

    /**
     * Test global statistics calculation.
     */
    public function test_get_stats_returns_correct_seeded_values()
    {
        $stats = $this->sessionService->getStats();
        
        // CSV has 2 sessions
        $this->assertEquals(2, $stats['total_sessions']);
        // Akajou is "terminée" in tickets.csv, Benani is "en cours", Mansouri is "en attente"
        // Total tickets: 3. Terminated: 1.
        // Percentage: (1/3)*100 = 33.33
        $this->assertEquals(33.33, $stats['presence_pourcentage']);
    }
}
