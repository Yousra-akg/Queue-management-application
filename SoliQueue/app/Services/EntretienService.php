<?php

namespace App\Services;

use App\Models\Entretien;
use App\Models\Ticket;
use App\Models\Candidat;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class EntretienService extends BaseService
{
    public function __construct(Entretien $model)
    {
        $this->model = $model;
    }

    /**
     * Recherche et filtre les entretiens.
     */
    public function searchAndFilter(string $search = '', string $statut = 'all')
    {
        return $this->model
            ->when($search, function ($query, $search) {
                return $query->whereDate('dateEntretien', $search);
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
        $totalEntretiens = $this->model->count();
        $entretiensTerminees = $this->model->where('statut', 'terminée')->count();
        $totalCandidats = Candidat::count();
        
        $totalTickets = Ticket::count();
        $ticketsTermines = Ticket::where('statut', 'terminée')->count();
        
        $presencePourcentage = $totalTickets > 0 
            ? round(($ticketsTermines / $totalTickets) * 100, 2) 
            : 0;

        return [
            'total_entretiens' => $totalEntretiens,
            'entretiens_terminees' => $entretiensTerminees,
            'total_candidats' => $totalCandidats,
            'presence_pourcentage' => $presencePourcentage,
        ];
    }

    /**
     * Crée une entretien.
     */
    public function createEntretien(array $data, int $userId): Entretien
    {
        $data['user_id'] = $userId;
        
        $affectations = $data['affectations'] ?? [];
        unset($data['affectations']);

        $entretien = $this->create($data);
        
        if (!empty($affectations)) {
            $formattedAffectations = [];
            foreach ($affectations as $aff) {
                if (!empty($aff['formateur_id']) && is_array($aff['formateur_id'])) {
                    foreach ($aff['formateur_id'] as $fId) {
                        $formattedAffectations[$fId] = ['salle_id' => $aff['salle_id'] ?? null];
                    }
                }
            }
            $entretien->formateurs()->attach($formattedAffectations);
        }

        return $entretien;
    }

    /**
     * Met à jour une entretien.
     */
    public function updateEntretien(int $id, array $data): Entretien
    {
        $affectations = $data['affectations'] ?? [];
        unset($data['affectations']);

        $entretien = $this->findOrFail($id);
        $entretien->update($data);
        
        if (isset($data['affectations']) || !empty($affectations)) {
            $entretien->formateurs()->detach();
            if (!empty($affectations)) {
                $formattedAffectations = [];
                foreach ($affectations as $aff) {
                    if (!empty($aff['formateur_id']) && is_array($aff['formateur_id'])) {
                        foreach ($aff['formateur_id'] as $fId) {
                            $formattedAffectations[$fId] = ['salle_id' => $aff['salle_id'] ?? null];
                        }
                    }
                }
                $entretien->formateurs()->attach($formattedAffectations);
            }
        }

        return $entretien;
    }

    /**
     * Supprime une entretien.
     */
    public function deleteEntretien(int $id): bool
    {
        $entretien = $this->findOrFail($id);
        return $entretien->delete();
    }

    /**
     * Calcule les statistiques complètes pour le tableau de bord Admin.
     */
    public function getDashboardStatsAdmin(): array
    {
        $totalCandidats = Candidat::count();
        $totalEntretiens = Entretien::count();
        $entretiensTerminees = Entretien::where('statut', 'terminée')->count();
        
        $totalPresents = Candidat::where('is_present', true)->count();
        $tauxPresence = $totalCandidats > 0 ? round(($totalPresents / $totalCandidats) * 100, 1) : 0;

        // Taux d'absentéisme par entretien
        $entretiensStats = Entretien::with('tickets')->get()->map(function($e) {
            $total = $e->tickets->count();
            $absents = $e->tickets->where('statut', 'absent')->count();
            $taux = $total > 0 ? round(($absents / $total) * 100, 1) : 0;
            return [
                'dateEntretien' => $e->dateEntretien,
                'taux_absenteisme' => $taux
            ];
        });

        // Temps moyen d'un entretien (en minutes)
        $ticketsTermines = Ticket::whereNotNull('heureAppel')->whereNotNull('heureFin')->get();
        $totalMinutes = 0;
        foreach($ticketsTermines as $t) {
            $totalMinutes += \Carbon\Carbon::parse($t->heureAppel)->diffInMinutes(\Carbon\Carbon::parse($t->heureFin));
        }
        $tempsMoyen = $ticketsTermines->count() > 0 ? round($totalMinutes / $ticketsTermines->count(), 1) : 0;

        // Heures de pointe d'arrivée des candidats
        $heuresArrivee = Ticket::whereNotNull('heureArrivee')
            ->get()
            ->groupBy(function($t) {
                return \Carbon\Carbon::parse($t->heureArrivee)->format('H') . 'h';
            })
            ->map->count();

        return [
            'totalCandidats' => $totalCandidats,
            'totalEntretiens' => $totalEntretiens,
            'entretiensTerminees' => $entretiensTerminees,
            'tauxPresence' => $tauxPresence,
            'entretiensStats' => $entretiensStats,
            'tempsMoyen' => $tempsMoyen,
            'heuresArrivee' => $heuresArrivee
        ];
    }

    /**
     * Génère un flux d'activité récent pour le tableau de bord Admin (Entretiens & Affectations).
     */
    public function getRecentActivitiesFeed(int $limit = 5)
    {
        $latestEntretiens = Entretien::orderBy('created_at', 'desc')->take(2)->get()->map(function($s) {
            return [
                'type' => 'entretien',
                'titre' => 'Nouvelle Entretien : ' . $s->dateEntretien,
                'temps' => $s->created_at->diffForHumans(),
                'couleur' => 'blue-600',
                'date' => $s->created_at
            ];
        });

        $latestAssignments = Candidat::whereNotNull('entretien_id')
            ->with('entretien')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($c) {
                return [
                    'type' => 'candidat',
                    'titre' => $c->prenom . ' ' . $c->nom . ' assigné à ' . ($c->entretien->dateEntretien ?? 'Entretien'),
                    'temps' => $c->updated_at->diffForHumans(),
                    'couleur' => 'emerald-500',
                    'date' => $c->updated_at
                ];
            });

        return $latestEntretiens->merge($latestAssignments)->sortByDesc('date')->take($limit);
    }

    /**
     * Met à jour les statuts basés sur le temps et retourne les entretiens triées.
     */
    public function getEntretiensWithStatusUpdate(): Collection
    {
        $entretiens = $this->model->orderBy('dateEntretien', 'desc')->get();
        foreach ($entretiens as $entretien) {
            $entretien->updateStatusBasedOnTime();
        }
        return $entretiens;
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

        $entretiens = Entretien::whereIn('statut', ['Active', 'En cours', 'Prête'])
            ->orderBy('dateEntretien', 'asc')
            ->get()
            ->map(function ($entretien) {
                // Trouver le candidat actuel (celui qui est en cours)
                $current = $entretien->tickets()
                    ->where('statut', 'en cours')
                    ->with('candidat')
                    ->first();
                
                // Trouver le suivant (le premier en attente par ordre de numéro)
                $next = $entretien->tickets()
                    ->where('statut', 'en attente')
                    ->with('candidat')
                    ->orderBy('numeroOrdre', 'asc')
                    ->first();
                
                return [
                    'id'               => $entretien->id,
                    'dateEntretien'    => $entretien->dateEntretien,
                    'statut'           => $entretien->statut,
                    'candidat_actuel'  => $current ? '#' . str_pad($current->numeroOrdre, 2, '0', STR_PAD_LEFT) : 'Aucun',
                    'prochain_candidat' => $next ? '#' . str_pad($next->numeroOrdre, 2, '0', STR_PAD_LEFT) : 'Aucun',
                ];
            });

        return [
            'stats' => $stats,
            'entretiens' => $entretiens
        ];
    }
}

