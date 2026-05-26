---
name: soliqueue-builder
description: Logique métier, services applicatifs, gestion transactionnelle des tickets et files d'attente de SoliQueue.
---

# ⚙️ COMPÉTENCE : SOLIQUEUE BUILDER

## Rôle et Domaine d'Action
Cette compétence pilote les opérations métier et assure le bon découpage fonctionnel dans la couche `app/Services/`. Elle gère les règles d'attribution de tickets, l'ordonnancement de la file d'attente et le calcul des statistiques de présence.

## Services Clés et Logique Applicative

### 1. Génération de Tickets (`TicketService`)
Le service génère un code unique séquentiel de type `SOLI-01` et attribue le numéro d'ordre incrémenté en assurant la non-duplication :
```php
public function generateTicket(int $candidatId)
{
    return DB::transaction(function () use ($candidatId) {
        $candidat = Candidat::findOrFail($candidatId);

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
            'heureArrivee' => now(),
        ]);
    });
}
```

### 2. Validation de Présence (`CandidatService`)
La confirmation de présence physique du candidat est protégée par la saisie du code secret unique de la session :
```php
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

        $candidat->update(['is_present' => true]);

        if ($candidat->ticket && $candidat->ticket->statut === 'en attente') {
            $candidat->ticket->update(['statut' => 'en cours']);
        }

        return $candidat;
    });
}
```

### 3. Ordonnancement de File d'Attente (`QueueService`)
Réorganise l'ordre d'attente à la volée suite à un glisser-déposer sur le dashboard :
```php
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
```
