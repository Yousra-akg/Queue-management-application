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
    public function getQueue(Request $request, $sessionId): JsonResponse
    {
        try {
            $queue = $this->ticketService->getLiveQueue($sessionId);
            
            $notifications = [];
            $candidatId = $request->query('candidate_id');
            if ($candidatId) {
                $notificationService = app(\App\Services\NotificationService::class);
                $notifications = $notificationService->getUnreadNotifications((int)$candidatId);
                \Illuminate\Support\Facades\Log::info("API: Notifications count for candidat_id {$candidatId} = " . count($notifications));
            } else {
                \Illuminate\Support\Facades\Log::warning("API: candidat_id is missing from getQueue request!");
            }

            return response()->json([
                'success' => true,
                'data'    => $queue,
                'notifications' => $notifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la file : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * POST /api/mobile/notifications/{id}/read
     * Marquer une notification comme lue via API Mobile.
     */
    public function markNotificationRead(int $id): JsonResponse
    {
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->markAsRead($id);
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification : ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/mobile/tickets/{id}
     * Retourne un ticket spécifique par son ID.
     */
    public function getTicket(int $id): JsonResponse
    {
        $ticket = \App\Models\Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket non trouvé.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $ticket
        ], 200);
    }
}
