<?php

namespace App\Services;

class MobileApiService extends BaseService
{
    public function login(string $cin)
    {
        return $this->post('login', [
            'cin' => $cin
        ]);
    }

    public function getRandomStudent()
    {
        return $this->get('random-student');
    }

    public function generateTicket(?int $etudiantId)
    {
        if (!$etudiantId) {
            throw new \Exception("ID étudiant manquant pour la génération du ticket.");
        }
        return $this->post('tickets/generate', [
            'etudiant_id' => $etudiantId
        ]);
    }

    public function getSessionStatus(int $sessionId)
    {
        return $this->get("sessions/{$sessionId}/status");
    }

    public function validatePresence(int $ticketId, string $codePresence)
    {
        return $this->post('tickets/validate-presence', [
            'ticket_id' => $ticketId,
            'code_presence' => $codePresence
        ]);
    }

    public function getLiveQueue(int $sessionId)
    {
        return $this->get("sessions/{$sessionId}/queue");
    }

    public function getDashboardStats()
    {
        return $this->get('admin/dashboard');
    }
}
