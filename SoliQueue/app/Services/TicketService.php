<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Candidat;
use App\Models\Entretien;
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

            // Vérifier si le candidat a déjà un ticket pour cette entretien
            $existingTicket = $this->model
                ->where('candidat_id', $candidatId)
                ->where('entretien_id', $candidat->entretien_id)
                ->first();

            if ($existingTicket) {
                return $existingTicket;
            }

            $maxOrder = $this->model
                ->where('entretien_id', $candidat->entretien_id)
                ->max('numeroOrdre') ?? 0;

            $lastTicketCount = $this->model->count();
            $codeUnique = 'SOLI-' . str_pad($lastTicketCount + 1, 2, '0', STR_PAD_LEFT);

            return $this->model->create([
                'candidat_id' => $candidatId,
                'entretien_id'  => $candidat->entretien_id,
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
        $entretien = $ticket->entretien;

        $codePresence = str_replace(' ', '', $codePresence);
        if ($entretien->codePresence !== $codePresence) {
            return false;
        }

        return $ticket->update(['statut' => 'en attente', 'heureArrivee' => Carbon::now()]);
    }

    
    public function getLiveQueue(int $entretienId)
    {
        return $this->model
            ->with(['candidat', 'salle'])
            ->where('entretien_id', $entretienId)
            ->orderBy('numeroOrdre', 'asc')
            ->get();
    }
}

