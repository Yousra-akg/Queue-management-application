<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class QueueService extends BaseService
{
    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    
    public function callNextCandidat(int $sessionId)
    {
        return DB::transaction(function () use ($sessionId) {
            $this->model
                ->where('session_id', $sessionId)
                ->where('statut', 'en cours')
                ->update(['statut' => 'terminée']);
            $nextTicket = $this->model
                ->where('session_id', $sessionId)
                ->where('statut', 'en attente')
                ->orderBy('numeroOrdre', 'asc')
                ->first();

            if ($nextTicket) {
                $nextTicket->update(['statut' => 'en cours']);
            }

            return $nextTicket;
        });
    }

    
    public function reorderQueue(int $sessionId, array $orderedTicketIds)
    {
        return DB::transaction(function () use ($sessionId, $orderedTicketIds) {
            foreach ($orderedTicketIds as $index => $ticketId) {
                $this->model
                    ->where('id', $ticketId)
                    ->where('session_id', $sessionId)
                    ->update(['numeroOrdre' => $index + 1]);
            }
            return true;
        });
    }

    
    public function updateCandidatStatus(int $ticketId, string $statut)
    {
        $validStatuses = ['en attente', 'en cours', 'terminée'];
        
        if (!in_array($statut, $validStatuses)) {
            throw new \InvalidArgumentException("Statut invalide.");
        }

        return $this->update($ticketId, ['statut' => $statut]);
    }
}
