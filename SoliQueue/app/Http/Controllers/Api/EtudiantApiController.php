<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidat;
use Illuminate\Http\JsonResponse;

class EtudiantApiController extends Controller
{
    /**
     * GET /api/mobile/random-student
     * Retourne un étudiant aléatoire (Candidat dans la base).
     */
    public function getRandomStudent(): JsonResponse
    {
        // Forcer l'ordre aléatoire (compatible avec SQLite)
        $etudiant = Candidat::orderByRaw('RANDOM()')->first();

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
