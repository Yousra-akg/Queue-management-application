<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;

class SessionApiController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * GET /api/mobile/sessions/{id}/status
     * Retourne les détails de la session, incluant dateEntretien pour le chrono.
     */
    public function getStatus($id): JsonResponse
    {
        $session = $this->sessionService->find($id);

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
        try {
            $data = $this->sessionService->getMobileDashboardStats();
            return response()->json([
                'success' => true,
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des stats : ' . $e->getMessage()
            ], 400);
        }
    }
}
