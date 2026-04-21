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

        return view('candidat.ticket-details', compact('candidat', 'session', 'ticket'));
    }

    /**
     * Validation de la présence physique.
     */
    public function markPresence()
    {
        $candidat = $this->candidatService->getAuthCandidatWithTicket();

        if (!$candidat) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        $this->candidatService->markPresence($candidat->id);

        return response()->json([
            'success' => true,
            'message' => 'Présence confirmée !',
            'time' => now()->format('H:i')
        ]);
    }
}
