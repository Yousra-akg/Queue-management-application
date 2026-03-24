<?php

namespace App\Services;

use App\Models\Candidat;
use App\Models\Session;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class CandidatService extends BaseService
{
    public function __construct(Candidat $model)
    {
        $this->model = $model;
    }

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

    
    public function getUnassigned()
    {
        return $this->model
            ->doesntHave('ticket')
            ->orderBy('created_at', 'desc')
            ->get();
    }

   
    public function getRecentActivity(int $limit = 10)
    {
        return Ticket::with(['candidat', 'session'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
