<?php

namespace App\Services;

use App\Models\Session;
use App\Models\Ticket;
use App\Models\Candidat;
use Illuminate\Support\Facades\DB;

class SessionService extends BaseService
{
    public function __construct(Session $model)
    {
        $this->model = $model;
    }

    
    public function searchAndFilter(string $search = '', string $statut = 'all')
    {
        return $this->model
            ->when($search, function ($query, $search) {
                return $query->where('nom', 'like', "%{$search}%");
            })
            ->when($statut !== 'all' && $statut !== '', function ($query) use ($statut) {
                return $query->where('statut', $statut);
            })
            ->orderBy('dateEntretien', 'desc')
            ->get();
    }

   
    public function getStats()
    {
        $totalSessions = $this->model->count();
        $sessionsTerminees = $this->model->where('statut', 'terminée')->count();
        $totalCandidats = Candidat::count();
        
        $totalTickets = Ticket::count();
        $ticketsTermines = Ticket::where('statut', 'terminée')->count();
        
        $presencePourcentage = $totalTickets > 0 
            ? round(($ticketsTermines / $totalTickets) * 100, 2) 
            : 0;

        return [
            'total_sessions' => $totalSessions,
            'sessions_terminees' => $sessionsTerminees,
            'total_candidats' => $totalCandidats,
            'presence_pourcentage' => $presencePourcentage,
        ];
    }
}
