<?php

namespace App\Http\Controllers;

use App\Services\CandidatService;
use App\Services\TicketService;
use Illuminate\Http\Request;

class CandidatPortalController extends Controller
{
    protected $candidatService;
    protected $ticketService;

    public function __construct(CandidatService $candidatService, TicketService $ticketService)
    {
        $this->candidatService = $candidatService;
        $this->ticketService = $ticketService;
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

        // Validation du code secret de la session
        $inputCode = str_replace(' ', '', $request->code);
        if ($candidat->session->codePresence !== $inputCode) {
            return response()->json(['success' => false, 'message' => 'Code de présence invalide.'], 422);
        }

        // Marquer la présence
        $this->candidatService->markPresence($candidat->id);
        
        // Mettre à jour le statut du ticket à "en attente" ou "en cours" ? 
        // L'énoncé suggère que la présence débloque l'affichage de la file.
        // On peut aussi passer le ticket à "en cours" si nécessaire.
        if ($candidat->ticket && $candidat->ticket->statut === 'en attente') {
            $candidat->ticket->update(['statut' => 'en cours']);
        }

        // Récupérer la file d'attente en temps réel
        $queue = $this->ticketService->getLiveQueue($candidat->session_id);

        return response()->json([
            'success' => true,
            'message' => 'Présence confirmée !',
            'time' => now()->format('H:i'),
            'queue' => $queue,
            'candidat_id' => $candidat->id
        ]);
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
        
        return response()->json([
            'success' => true,
            'queue' => $queue,
            'my_ticket_status' => $candidat->ticket->statut ?? 'en attente',
            'my_position' => $queue->where('candidat_id', $candidat->id)->keys()->first() + 1
        ]);
    }
}
