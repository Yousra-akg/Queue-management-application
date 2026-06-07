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

    public function callNextCandidat(int $entretienId, ?int $formateurId = null)
    {
        return DB::transaction(function () use ($entretienId, $formateurId) {
            $currentActive = $this->model
                ->where('entretien_id', $entretienId)
                ->where('statut', 'en cours')
                ->first();

            if ($currentActive) {
                $currentActive->update([
                    'statut' => 'terminée',
                    'heureFin' => now()
                ]);
                $this->notificationService->createNotification(
                    $currentActive->candidat_id,
                    "Entretien Terminé",
                    "Merci pour votre passage. Votre résultat sera affiché bientôt."
                );
            }

            $nextTicket = $this->model
                ->where('entretien_id', $entretienId)
                ->where('statut', 'en attente')
                ->orderBy('numeroOrdre', 'asc')
                ->first();

            if ($nextTicket) {
                $salleId = null;
                if ($formateurId) {
                    $affectation = DB::table('entretien_formateur_salle')
                        ->where('entretien_id', $entretienId)
                        ->where('formateur_id', $formateurId)
                        ->first();
                    if ($affectation) {
                        $salleId = $affectation->salle_id;
                    }
                }

                $nextTicket->update([
                    'statut' => 'en cours',
                    'heureAppel' => now(),
                    'formateur_id' => $formateurId,
                    'salle_id' => $salleId
                ]);

                $salleNom = $salleId ? \App\Models\Salle::find($salleId)->nom : 'la salle d\'entretien';

                $this->notificationService->createNotification(
                    $nextTicket->candidat_id,
                    "C'est votre tour !",
                    "Veuillez vous présenter à " . $salleNom . " immédiatement."
                );

                // Notifier le candidat suivant immédiat (le premier en attente désormais)
                $upcomingTicket = $this->model
                    ->where('entretien_id', $entretienId)
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

    public function reorderQueue(int $entretienId, array $orderedTicketIds)
    {
        return DB::transaction(function () use ($entretienId, $orderedTicketIds) {
            $oldTickets = $this->model
                ->where('entretien_id', $entretienId)
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

    public function updateCandidatStatus(int $ticketId, string $statut, ?int $formateurId = null)
    {
        $validStatuses = ['en attente', 'en cours', 'terminée', 'absent'];
        
        if (!in_array($statut, $validStatuses)) {
            throw new \InvalidArgumentException("Statut invalide.");
        }

        $ticket = $this->findOrFail($ticketId);
        $oldStatut = $ticket->statut;
        
        $updateData = ['statut' => $statut];
        if ($statut === 'en cours') {
            $updateData['heureAppel'] = now();
            if ($formateurId) {
                $updateData['formateur_id'] = $formateurId;
                $affectation = DB::table('entretien_formateur_salle')
                    ->where('entretien_id', $ticket->entretien_id)
                    ->where('formateur_id', $formateurId)
                    ->first();
                if ($affectation) {
                    $updateData['salle_id'] = $affectation->salle_id;
                }
            }
        } elseif ($statut === 'terminée' || $statut === 'absent') {
            $updateData['heureFin'] = now();
        }

        $ticket->update($updateData);

        if ($oldStatut !== $statut) {
            if ($statut === 'en cours') {
                $salleNom = isset($updateData['salle_id']) ? \App\Models\Salle::find($updateData['salle_id'])->nom : 'la salle d\'entretien';
                $this->notificationService->createNotification(
                    $ticket->candidat_id,
                    "C'est votre tour !",
                    "Veuillez vous présenter à " . $salleNom . " immédiatement."
                );

                // Notifier le candidat suivant immédiat
                $upcomingTicket = $this->model
                    ->where('entretien_id', $ticket->entretien_id)
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


