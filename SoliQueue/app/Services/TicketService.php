<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Candidat;
use App\Models\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketService extends BaseService
{
    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    
    public function generateTicket(int $candidatId)
    {
        return DB::transaction(function () use ($candidatId) {
            $candidat = Candidat::findOrFail($candidatId);

            // Vérifier si le candidat a déjà un ticket pour cette session
            $existingTicket = $this->model
                ->where('candidat_id', $candidatId)
                ->where('session_id', $candidat->session_id)
                ->first();

            if ($existingTicket) {
                return $existingTicket;
            }

            $maxOrder = $this->model
                ->where('session_id', $candidat->session_id)
                ->max('numeroOrdre') ?? 0;

            $lastTicketCount = $this->model->count();
            $codeUnique = 'SOLI-' . str_pad($lastTicketCount + 1, 2, '0', STR_PAD_LEFT);

            return $this->model->create([
                'candidat_id' => $candidatId,
                'session_id'  => $candidat->session_id,
                'codeUnique'  => $codeUnique,
                'numeroOrdre' => $maxOrder + 1,
                'statut'      => 'en attente',
                'heureArrivee' => Carbon::now(),
            ]);
        });
    }

    
    public function validatePresence(int $ticketId, string $codePresence)
    {
        $ticket = $this->findOrFail($ticketId);
        $session = $ticket->session;

        $codePresence = str_replace(' ', '', $codePresence);
        if ($session->codePresence !== $codePresence) {
            return false;
        }

        return $ticket->update(['statut' => 'en attente', 'heureArrivee' => Carbon::now()]);
    }

    
    public function getLiveQueue(int $sessionId)
    {
        return $this->model
            ->with('candidat')
            ->where('session_id', $sessionId)
            ->orderBy('numeroOrdre', 'asc')
            ->get();
    }
}
