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

    /**
     * POST /api/mobile/login
     * Authentifie un candidat avec son CIN.
     */
    public function login(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'cin' => 'required|string'
        ]);

        $cin = trim($request->input('cin'));
        $etudiant = \App\Models\Candidat::where('cin', $cin)->first();

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun candidat trouvé avec ce CIN.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $etudiant
        ], 200);
    }
}
