<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class QueueService extends BaseService
{
    protected $notificationService;

    public function __construct(Ticket $model, NotificationService $notificationService)
    {
        $this->model = $model;
        $this->notificationService = $notificationService;
    }

    public function callNextCandidat(int $sessionId)
    {
        return DB::transaction(function () use ($sessionId) {
            $currentActive = $this->model
                ->where('session_id', $sessionId)
                ->where('statut', 'en cours')
                ->first();

            if ($currentActive) {
                $currentActive->update(['statut' => 'terminée']);
                $this->notificationService->createNotification(
                    $currentActive->candidat_id,
                    "Entretien Terminé",
                    "Merci pour votre passage. Votre résultat sera affiché bientôt."
                );
            }

            $nextTicket = $this->model
                ->where('session_id', $sessionId)
                ->where('statut', 'en attente')
                ->orderBy('numeroOrdre', 'asc')
                ->first();

            if ($nextTicket) {
                $nextTicket->update(['statut' => 'en cours']);
                $this->notificationService->createNotification(
                    $nextTicket->candidat_id,
                    "C'est votre tour !",
                    "Veuillez vous présenter à la salle d'entretien immédiatement."
                );

                // Notifier le candidat suivant immédiat (le premier en attente désormais)
                $upcomingTicket = $this->model
                    ->where('session_id', $sessionId)
                    ->where('statut', 'en attente')
                    ->orderBy('numeroOrdre', 'asc')
                    ->first();

                if ($upcomingTicket) {
                    $this->notificationService->createNotification(
                        $upcomingTicket->candidat_id,
                        "Votre tour approche !",
                        "Vous êtes le prochain sur la liste. Tenez-vous prêt !"
                    );
                }
            }

            return $nextTicket;
        });
    }

    public function reorderQueue(int $sessionId, array $orderedTicketIds)
    {
        return DB::transaction(function () use ($sessionId, $orderedTicketIds) {
            $oldTickets = $this->model
                ->where('session_id', $sessionId)
                ->pluck('numeroOrdre', 'id')
                ->toArray();

            foreach ($orderedTicketIds as $index => $ticketId) {
                $newOrder = $index + 1;
                $oldOrder = $oldTickets[$ticketId] ?? null;

                if ($oldOrder !== $newOrder) {
                    $ticket = $this->model->find($ticketId);
                    if ($ticket) {
                        $ticket->update(['numeroOrdre' => $newOrder]);
                        $this->notificationService->createNotification(
                            $ticket->candidat_id,
                            "Ordre de passage mis à jour",
                            "Le formateur a modifié l'ordre de passage. Votre nouvelle position est la position " . $newOrder . "."
                        );
                    }
                }
            }
            return true;
        });
    }

    public function updateCandidatStatus(int $ticketId, string $statut)
    {
        $validStatuses = ['en attente', 'en cours', 'terminée', 'absent'];
        
        if (!in_array($statut, $validStatuses)) {
            throw new \InvalidArgumentException("Statut invalide.");
        }

        $ticket = $this->findOrFail($ticketId);
        $oldStatut = $ticket->statut;
        $ticket->update(['statut' => $statut]);

        if ($oldStatut !== $statut) {
            if ($statut === 'en cours') {
                $this->notificationService->createNotification(
                    $ticket->candidat_id,
                    "C'est votre tour !",
                    "Veuillez vous présenter à la salle d'entretien immédiatement."
                );

                // Notifier le candidat suivant immédiat
                $upcomingTicket = $this->model
                    ->where('session_id', $ticket->session_id)
                    ->where('statut', 'en attente')
                    ->orderBy('numeroOrdre', 'asc')
                    ->first();

                if ($upcomingTicket) {
                    $this->notificationService->createNotification(
                        $upcomingTicket->candidat_id,
                        "Votre tour approche !",
                        "Vous êtes le prochain sur la liste. Tenez-vous prêt !"
                    );
                }
            } elseif ($statut === 'terminée') {
                $this->notificationService->createNotification(
                    $ticket->candidat_id,
                    "Entretien Terminé",
                    "Merci pour votre passage. Votre résultat sera affiché bientôt."
                );
            } elseif ($statut === 'absent') {
                $this->notificationService->createNotification(
                    $ticket->candidat_id,
                    "Candidat Absent",
                    "Vous avez été marqué comme absent à l'entretien."
                );
            }
        }

        return $ticket;
    }
}

