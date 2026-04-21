<?php

namespace App\Services;

use App\Models\Candidat;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as LaravelSession;

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

        if (!$candidat->session_id) {
            throw new \Exception("Ce candidat n'est pas encore affecté à une session d'entretien.");
        }

        // Authentification via session Laravel
        LaravelSession::put('candidat_id', $candidat->id);

        return $candidat;
    }

    /**
     * Récupère le candidat actuellement connecté avec ses relations.
     * 
     * @return Candidat|null
     */
    public function getAuthCandidatWithTicket(): ?Candidat
    {
        $candidatId = LaravelSession::get('candidat_id');

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
            ->doesntHave('ticket')
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
}
