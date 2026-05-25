<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CandidatService;
use Illuminate\Http\JsonResponse;

class EtudiantApiController extends Controller
{
    protected $candidatService;

    public function __construct(CandidatService $candidatService)
    {
        $this->candidatService = $candidatService;
    }

    /**
     * GET /api/mobile/random-student
     * Retourne un étudiant aléatoire (Candidat dans la base).
     */
    public function getRandomStudent(): JsonResponse
    {
        $etudiant = $this->candidatService->getRandomCandidate();

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun étudiant trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $etudiant
        ], 200);
    }
}
