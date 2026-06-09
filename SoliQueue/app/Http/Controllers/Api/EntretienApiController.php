<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EntretienService;
use Illuminate\Http\JsonResponse;

class EntretienApiController extends Controller
{
    protected $entretienService;

    public function __construct(EntretienService $entretienService)
    {
        $this->entretienService = $entretienService;
    }

    /**
     * GET /api/mobile/entretiens/{id}/status
     * Retourne les détails de la entretien, incluant dateEntretien pour le chrono.
     */
    public function getStatus($id): JsonResponse
    {
        $entretien = $this->entretienService->find($id);

        if (!$entretien) {
            return response()->json([
                'success' => false,
                'message' => 'Entretien introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $entretien->id,
                'statut'        => $entretien->statut,
                'dateEntretien' => $entretien->dateEntretien,
                'heureDebut'    => $entretien->heureDebut,
                'capaciteMax'   => $entretien->capaciteMax
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
            $data = $this->entretienService->getMobileDashboardStats();
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

