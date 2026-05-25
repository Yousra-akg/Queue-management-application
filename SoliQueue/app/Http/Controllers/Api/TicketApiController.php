<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketApiController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * POST /api/mobile/tickets/generate
     * Prend un etudiant_id (candidat_id dans DB), génère un ticket et retourne les infos.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'etudiant_id' => 'required|integer|exists:candidats,id'
        ]);

        try {
            // Génération du ticket via le service existant
            $ticket = $this->ticketService->generateTicket($request->etudiant_id);
            
            return response()->json([
                'success' => true,
                'data'    => $ticket,
                'message' => 'Ticket généré avec succès'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * POST /api/mobile/tickets/validate-presence
     * Prend ticket_id et code_presence. Si valide, met à jour le statut.
     */
    public function validatePresence(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_id'     => 'required|integer|exists:tickets,id',
            'code_presence' => 'required|string'
        ]);

        try {
            $isValid = $this->ticketService->validatePresence(
                $request->ticket_id, 
                $request->code_presence
            );

            if ($isValid) {
                return response()->json([
                    'success' => true,
                    'message' => 'Présence validée, ticket en cours.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Code de présence invalide.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/mobile/sessions/{id}/queue
     * Retourne la file d'attente en direct pour une session.
     */
    public function getQueue($sessionId): JsonResponse
    {
        try {
            $queue = $this->ticketService->getLiveQueue($sessionId);
            return response()->json([
                'success' => true,
                'data'    => $queue
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la file : ' . $e->getMessage()
            ], 400);
        }
    }
}
