<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class SessionApiController extends Controller
{
    /**
     * GET /api/mobile/sessions/{id}/status
     * Retourne les détails de la session, incluant dateEntretien pour le chrono.
     */
    public function getStatus($id): JsonResponse
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $session->id,
                'nom'           => $session->nom,
                'statut'        => $session->statut,
                'dateEntretien' => $session->dateEntretien,
                'capaciteMax'   => $session->capaciteMax
            ]
        ], 200);
    }

    /**
     * GET /api/mobile/admin/dashboard
     * Retourne les statistiques globales (Tickets émis, En attente, Traités, Absents)
     */
    public function getDashboardStats(): JsonResponse
    {
        // 1. Calcul des métriques globales
        $stats = [
            'tickets_emis' => Ticket::count(),
            'en_attente'   => Ticket::where('statut', 'en attente')->count(),
            'traites'      => Ticket::where('statut', 'terminée')->count(),
            'absents'      => Ticket::where('statut', 'absent')->count()
        ];
        
        // 2. Récupération des sessions actives avec détails (UC8)
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

        return response()->json([
            'success' => true,
            'data'    => [
                'stats'    => $stats,
                'sessions' => $sessions
            ]
        ], 200);
    }
}
