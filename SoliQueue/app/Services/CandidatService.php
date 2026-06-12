<?php

namespace App\Services;

use App\Models\Candidat;
use App\Models\Entretien;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Entretien as LaravelEntretien;
use Illuminate\Support\Facades\Storage;

class CandidatService extends BaseService
{
    public function __construct(Candidat $model)
    {
        $this->model = $model;
    }

    /**
     * Authentifie un candidat par son CIN.
     * 
     * @param string $cin
     * @return Candidat
     * @throws \Exception
     */
    public function loginByCin(string $cin): Candidat
    {
        $candidat = $this->model->where('cin', $cin)->first();

        if (!$candidat) {
            throw new \Exception("Aucun candidat trouvé avec ce CIN.");
        }

        return $candidat;
    }

    /**
     * Récupère le candidat actuellement connecté avec ses relations.
     * 
     * @return Candidat|null
     */
    public function getAuthCandidatWithTicket(): ?Candidat
    {
        $candidatId = \Illuminate\Support\Facades\Auth::id();

        if (!$candidatId) {
            return null;
        }

        return $this->model->with(['entretien', 'ticket.salle'])->find($candidatId);
    }

    /**
     * Marque la présence physique du candidat.
     * 
     * @param int $id
     * @return bool
     */
    public function markPresence(int $id): bool
    {
        $candidat = $this->findOrFail($id);
        return $candidat->update(['is_present' => true]);
    }

    /**
     * Assigne un candidat à une entretien.
     */
    public function assignToEntretien(int $candidatId, int $entretienId)
    {
        return DB::transaction(function () use ($candidatId, $entretienId) {
            $candidat = $this->findOrFail($candidatId);
            $entretien = Entretien::findOrFail($entretienId);

            $currentCount = $entretien->candidats()->count();
            if ($currentCount >= $entretien->capaciteMax) {
                throw new \Exception("La entretien a atteint sa capacité maximale.");
            }

            return $candidat->update(['entretien_id' => $entretienId]);
        });
    }

    /**
     * Récupère les candidats sans ticket.
     */
    public function getUnassigned()
    {
        return $this->model
             ->whereNull('entretien_id')
             ->orderBy('created_at', 'desc')
             ->get();
    }

    /**
     * Récupère l'activité récente (tickets).
     */
    public function getRecentActivity(int $limit = 10)
    {
        return Ticket::with(['candidat', 'entretien'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Crée un candidat et gère l'upload de sa photo.
     */
    public function createCandidate(array $data, $photoFile = null): Candidat
    {
        if ($photoFile) {
            $data['photo'] = $photoFile->store('candidats', 'public');
        }
        return $this->create($data);
    }

    /**
     * Met à jour un candidat et remplace sa photo s'il y en a une nouvelle.
     */
    public function updateCandidate(int $id, array $data, $photoFile = null): Candidat
    {
        $candidat = $this->findOrFail($id);

        if ($photoFile) {
            if ($candidat->photo) {
                Storage::disk('public')->delete($candidat->photo);
            }
            $data['photo'] = $photoFile->store('candidats', 'public');
        }

        $candidat->update($data);
        return $candidat;
    }

    /**
     * Supprime un candidat et sa photo de stockage.
     */
    public function deleteCandidate(int $id): bool
    {
        $candidat = $this->findOrFail($id);

        if ($candidat->photo) {
            Storage::disk('public')->delete($candidat->photo);
        }

        return $candidat->delete();
    }

    /**
     * Assigne plusieurs candidats à une entretien et génère leurs tickets.
     */
    public function assignCandidatesToEntretien(int $entretienId, array $candidateIds): bool
    {
        return DB::transaction(function () use ($entretienId, $candidateIds) {
            $entretien = Entretien::findOrFail($entretienId);
            $currentCount = $entretien->candidats()->count();
            $newCount = count($candidateIds);

            if ($currentCount + $newCount > $entretien->capaciteMax) {
                throw new \Exception('La entretien a atteint sa capacité maximale (' . $entretien->capaciteMax . ' places).');
            }

            $ticketService = app(TicketService::class);
            foreach ($candidateIds as $candidateId) {
                $candidat = $this->findOrFail($candidateId);
                $candidat->update(['entretien_id' => $entretienId]);
                $ticketService->generateTicket($candidat->id);
            }

            return true;
        });
    }

    /**
     * Désassigne un candidat d'une entretien et supprime son ticket.
     */
    public function unassignCandidateFromEntretien(int $candidatId): bool
    {
        return DB::transaction(function () use ($candidatId) {
            $candidat = $this->findOrFail($candidatId);

            // Bloquer le retrait si la entretien est terminée et que le candidat était présent
            if ($candidat->entretien && $candidat->entretien->statut === 'terminée' && $candidat->is_present) {
                throw new \Exception("Impossible de retirer un candidat présent d'une entretien terminée.");
            }

            $candidat->update(['entretien_id' => null]);
            $candidat->ticket()->delete();

            return true;
        });
    }

    /**
     * Retourne un candidat aléatoire pour l'API mobile.
     */
    public function getRandomCandidate(): ?Candidat
    {
        return $this->model->inRandomOrder()->first();
    }

    /**
     * Valide le code secret et confirme la présence du candidat.
     */
    public function validateAndConfirmPresence(int $candidatId, string $code): Candidat
    {
        return DB::transaction(function () use ($candidatId, $code) {
            $candidat = $this->model->with(['entretien', 'ticket'])->findOrFail($candidatId);

            if (!$candidat->entretien) {
                throw new \Exception("Vous n'êtes affecté à aucune entretien pour le moment.");
            }

            $inputCode = strtoupper(str_replace(' ', '', $code));
            if (strtoupper($candidat->entretien->codePresence) !== $inputCode) {
                throw new \Exception("Code de présence invalide.");
            }

            // Marquer la présence
            $candidat->update(['is_present' => true]);



            return $candidat;
        });
    }
}

