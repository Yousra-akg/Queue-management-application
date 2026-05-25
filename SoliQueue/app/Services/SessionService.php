<?php

namespace App\Services;

use App\Models\Session;
use App\Models\Ticket;
use App\Models\Candidat;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class SessionService extends BaseService
{
    public function __construct(Session $model)
    {
        $this->model = $model;
    }

    /**
     * Recherche et filtre les sessions.
     */
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

    /**
     * Calcule les statistiques simples.
     */
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

    /**
     * Crée une session.
     */
    public function createSession(array $data, int $userId): Session
    {
        $data['user_id'] = $userId;
        
        if (stripos($data['nom'], 'session ') !== 0) {
            $data['nom'] = 'Session ' . ucfirst($data['nom']);
        }

        return $this->create($data);
    }

    /**
     * Met à jour une session.
     */
    public function updateSession(int $id, array $data): Session
    {
        if (stripos($data['nom'], 'session ') !== 0) {
            $data['nom'] = 'Session ' . ucfirst($data['nom']);
        }

        $session = $this->findOrFail($id);
        $session->update($data);
        return $session;
    }

    /**
     * Supprime une session.
     */
    public function deleteSession(int $id): bool
    {
        $session = $this->findOrFail($id);
        return $session->delete();
    }

    /**
     * Calcule les statistiques complètes pour le tableau de bord Admin.
     */
    public function getDashboardStatsAdmin(): array
    {
        $totalCandidats = Candidat::count();
        $totalSessions = Session::count();
        $sessionsTerminees = Session::where('statut', 'terminée')->count();
        
        $totalPresents = Candidat::where('is_present', true)->count();
        $tauxPresence = $totalCandidats > 0 ? round(($totalPresents / $totalCandidats) * 100, 1) : 0;

        return [
            'totalCandidats' => $totalCandidats,
            'totalSessions' => $totalSessions,
            'sessionsTerminees' => $sessionsTerminees,
            'tauxPresence' => $tauxPresence,
        ];
    }

    /**
     * Génère un flux d'activité récent pour le tableau de bord Admin (Sessions & Affectations).
     */
    public function getRecentActivitiesFeed(int $limit = 5)
    {
        $latestSessions = Session::orderBy('created_at', 'desc')->take(2)->get()->map(function($s) {
            return [
                'type' => 'session',
                'titre' => 'Nouvelle Session : ' . $s->nom,
                'temps' => $s->created_at->diffForHumans(),
                'couleur' => 'blue-600',
                'date' => $s->created_at
            ];
        });

        $latestAssignments = Candidat::whereNotNull('session_id')
            ->with('session')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($c) {
                return [
                    'type' => 'candidat',
                    'titre' => $c->prenom . ' ' . $c->nom . ' assigné à ' . ($c->session->nom ?? 'Session'),
                    'temps' => $c->updated_at->diffForHumans(),
                    'couleur' => 'emerald-500',
                    'date' => $c->updated_at
                ];
            });

        return $latestSessions->merge($latestAssignments)->sortByDesc('date')->take($limit);
    }

    /**
     * Met à jour les statuts basés sur le temps et retourne les sessions triées.
     */
    public function getSessionsWithStatusUpdate(): Collection
    {
        $sessions = $this->model->orderBy('dateEntretien', 'desc')->get();
        foreach ($sessions as $session) {
            $session->updateStatusBasedOnTime();
        }
        return $sessions;
    }

    /**
     * Calcule les statistiques globales pour l'API mobile Dashboard.
     */
    public function getMobileDashboardStats(): array
    {
        $stats = [
            'tickets_emis' => Ticket::count(),
            'en_attente'   => Ticket::where('statut', 'en attente')->count(),
            'traites'      => Ticket::where('statut', 'terminée')->count(),
            'absents'      => Ticket::where('statut', 'absent')->count()
        ];

        $sessions = Session::whereIn('statut', ['Active', 'En cours', 'Prête'])
            ->orderBy('dateEntretien', 'asc')
            ->get()
            ->map(function ($session) {
                // Trouver le candidat actuel (celui qui est en cours)
                $current = $session->tickets()
                    ->where('statut', 'en cours')
                    ->with('candidat')
                    ->first();
                
                // Trouver le suivant (le premier en attente par ordre de numéro)
                $next = $session->tickets()
                    ->where('statut', 'en attente')
                    ->with('candidat')
                    ->orderBy('numeroOrdre', 'asc')
                    ->first();
                
                return [
                    'id'               => $session->id,
                    'nom'              => $session->nom,
                    'statut'           => $session->statut,
                    'candidat_actuel'  => $current ? '#' . str_pad($current->numeroOrdre, 2, '0', STR_PAD_LEFT) : 'Aucun',
                    'prochain_candidat' => $next ? '#' . str_pad($next->numeroOrdre, 2, '0', STR_PAD_LEFT) : 'Aucun',
                ];
            });

        return [
            'stats' => $stats,
            'sessions' => $sessions
        ];
    }
}
