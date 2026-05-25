<?php

namespace App\Services;

use App\Models\Candidat;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as LaravelSession;
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

        return $this->model->with(['session', 'ticket'])->find($candidatId);
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
     * Assigne un candidat à une session.
     */
    public function assignToSession(int $candidatId, int $sessionId)
    {
        return DB::transaction(function () use ($candidatId, $sessionId) {
            $candidat = $this->findOrFail($candidatId);
            $session = Session::findOrFail($sessionId);

            $currentCount = $session->candidats()->count();
            if ($currentCount >= $session->capaciteMax) {
                throw new \Exception("La session a atteint sa capacité maximale.");
            }

            return $candidat->update(['session_id' => $sessionId]);
        });
    }

    /**
     * Récupère les candidats sans ticket.
     */
    public function getUnassigned()
    {
        return $this->model
             ->whereNull('session_id')
             ->orderBy('created_at', 'desc')
             ->get();
    }

    /**
     * Récupère l'activité récente (tickets).
     */
    public function getRecentActivity(int $limit = 10)
    {
        return Ticket::with(['candidat', 'session'])
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
     * Assigne plusieurs candidats à une session et génère leurs tickets.
     */
    public function assignCandidatesToSession(int $sessionId, array $candidateIds): bool
    {
        return DB::transaction(function () use ($sessionId, $candidateIds) {
            $session = Session::findOrFail($sessionId);
            $currentCount = $session->candidats()->count();
            $newCount = count($candidateIds);

            if ($currentCount + $newCount > $session->capaciteMax) {
                throw new \Exception('La session a atteint sa capacité maximale (' . $session->capaciteMax . ' places).');
            }

            $ticketService = app(TicketService::class);
            foreach ($candidateIds as $candidateId) {
                $candidat = $this->findOrFail($candidateId);
                $candidat->update(['session_id' => $sessionId]);
                $ticketService->generateTicket($candidat->id);
            }

            return true;
        });
    }

    /**
     * Désassigne un candidat d'une session et supprime son ticket.
     */
    public function unassignCandidateFromSession(int $candidatId): bool
    {
        return DB::transaction(function () use ($candidatId) {
            $candidat = $this->findOrFail($candidatId);

            // Bloquer le retrait si la session est terminée et que le candidat était présent
            if ($candidat->session && $candidat->session->statut === 'terminée' && $candidat->is_present) {
                throw new \Exception("Impossible de retirer un candidat présent d'une session terminée.");
            }

            $candidat->update(['session_id' => null]);
            $candidat->ticket()->delete();

            return true;
        });
    }

    /**
     * Retourne un candidat aléatoire pour l'API mobile.
     */
    public function getRandomCandidate(): ?Candidat
    {
        return $this->model->orderByRaw('RANDOM()')->first();
    }

    /**
     * Valide le code secret et confirme la présence du candidat.
     */
    public function validateAndConfirmPresence(int $candidatId, string $code): Candidat
    {
        return DB::transaction(function () use ($candidatId, $code) {
            $candidat = $this->model->with(['session', 'ticket'])->findOrFail($candidatId);

            if (!$candidat->session) {
                throw new \Exception("Vous n'êtes affecté à aucune session pour le moment.");
            }

            $inputCode = str_replace(' ', '', $code);
            if ($candidat->session->codePresence !== $inputCode) {
                throw new \Exception("Code de présence invalide.");
            }

            // Marquer la présence
            $candidat->update(['is_present' => true]);

            // Mettre à jour le statut du ticket à "en cours" si en attente
            if ($candidat->ticket && $candidat->ticket->statut === 'en attente') {
                $candidat->ticket->update(['statut' => 'en cours']);
            }

            return $candidat;
        });
    }
}
