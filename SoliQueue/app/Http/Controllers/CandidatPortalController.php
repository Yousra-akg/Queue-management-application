<?php

namespace App\Http\Controllers;

use App\Services\CandidatService;
use App\Services\TicketService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CandidatPortalController extends Controller
{
    protected $candidatService;
    protected $ticketService;
    protected $notificationService;

    public function __construct(
        CandidatService $candidatService,
        TicketService $ticketService,
        NotificationService $notificationService
    ) {
        $this->candidatService = $candidatService;
        $this->ticketService = $ticketService;
        $this->notificationService = $notificationService;
    }

    /**
     * Étape 2 : Page de bienvenue.
     */
    public function bienvenue()
    {
        $candidat = $this->candidatService->getAuthCandidatWithTicket();
        
        if (!$candidat) {
            return redirect()->route('login');
        }

        return view('candidat.bienvenue', compact('candidat'));
    }

    /**
     * Étape 3 : Détails du ticket et compte à rebours.
     */
    public function ticketDetails()
    {
        $candidat = $this->candidatService->getAuthCandidatWithTicket();

        if (!$candidat) {
            return redirect()->route('login');
        }

        if (!$candidat->session_id) {
            return redirect()->route('candidat.bienvenue')->with('error', 'Vous n\'êtes affecté à aucune session pour le moment.');
        }

        // Si le ticket n'existe pas encore, on le génère
        if (!$candidat->ticket) {
            $this->ticketService->generateTicket($candidat->id);
            $candidat->refresh(); // Recharger avec le ticket
        }

        $session = $candidat->session;
        $ticket = $candidat->ticket;

        $queue = [];
        if ($candidat->is_present) {
            $queue = $this->ticketService->getLiveQueue($candidat->session_id);
        }

        return view('candidat.ticket-details', compact('candidat', 'session', 'ticket', 'queue'));
    }

    /**
     * Validation de la présence physique avec code secret.
     */
    public function markPresence(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $candidat = $this->candidatService->getAuthCandidatWithTicket();

        if (!$candidat) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        try {
            $candidat = $this->candidatService->validateAndConfirmPresence($candidat->id, $request->code);
            $queue = $this->ticketService->getLiveQueue($candidat->session_id);

            return response()->json([
                'success' => true,
                'message' => 'Présence confirmée !',
                'time' => now()->format('H:i'),
                'queue' => $queue,
                'candidat_id' => $candidat->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Endpoint pour le polling de l'état de la file d'attente (live update).
     */
    public function getQueueStatus()
    {
        $candidat = $this->candidatService->getAuthCandidatWithTicket();
        
        if (!$candidat || !$candidat->is_present) {
            return response()->json(['success' => false], 401);
        }

        $queue = $this->ticketService->getLiveQueue($candidat->session_id);
        $notifications = $this->notificationService->getUnreadNotifications($candidat->id);
        
        return response()->json([
            'success' => true,
            'queue' => $queue,
            'my_ticket_status' => $candidat->ticket->statut ?? 'en attente',
            'my_position' => $queue->where('candidat_id', $candidat->id)->keys()->first() + 1,
            'notifications' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markNotificationRead(int $notificationId)
    {
        try {
            $this->notificationService->markAsRead($notificationId);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

