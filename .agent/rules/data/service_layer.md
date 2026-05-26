---
trigger: always_on
type: rule
id: soliqueue-service-layer
---

# CONVENTIONS DE LA COUCHE SERVICES (SERVICE LAYER) - SOLIQUEUE

## Rôles et Responsabilités

### Contrôleurs (Http/Controllers/)
*   **Unique responsabilité** : Valider les entrées de requêtes HTTP (validation de base de données, validation de types), déléguer immédiatement la logique métier et d'écriture aux services correspondants, puis retourner la réponse correspondante (vue Blade, redirection flash ou réponse JSON/Axios).
*   **Interdiction** : Aucune transaction directe (`DB::transaction`), aucune manipulation de fichiers physiques (`Storage::disk`), pas de calcul de métriques complexes, pas de requêtes complexes Eloquent.

### Services (Services/)
*   **Unique responsabilité** : Centraliser toute la logique métier.
*   Ils gèrent le stockage et la suppression physique des photos candidats, les transactions complexes de base de données, la file d'attente en temps réel, le calcul des KPIs des tableaux de bord, et l'application des contraintes de sécurité (comme interdire de retirer un candidat d'une session fermée).
*   Chaque méthode de service doit être rigoureusement typée.

## Référentiel des Services Existants

| Service | Fonctions Principales et Rôles |
|---|---|
| `BaseService` | Fournit les helpers d'accès génériques (`all`, `find`, `findOrFail`, `create`, `update`, `delete`). |
| `FormateurService` | CRUD des formateurs (creation de l'utilisateur relié, hachage du mot de passe, synchronisation automatique du rôle Spatie `formateur` et transactions de suppression). |
| `CandidatService` | CRUD des candidats avec téléversement/suppression de photo de profil, assignation globale de candidats, blocage de désaffectation sur session terminée, récupération d'étudiant aléatoire, validation de code de présence et déblocage de ticket. |
| `SessionService` | CRUD des sessions, calcul des KPIs d'administration (taux de présence, sessions terminées), agrégation du flux d'activités récentes en direct, mise à jour temporelle automatique des statuts, et KPIs mobile. |
| `QueueService` | Gestion de l'appel du candidat suivant (mise à jour transactionnelle des statuts), réorganisation de l'ordre d'attente (Sortable), et mise à jour de statut de ticket. |
| `TicketService` | Génération automatique de code unique (`SOLI-XX`) et de numéro d'ordre incrémenté, validation manuelle/mobile de code présence, et récupération de la file d'attente ordonnée en direct (`getLiveQueue`). |

## Exemple de Transaction Standard dans CandidatService (Assignation multiple)

```php
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
```
