---
description: Guide du portail formateur, sélection de session et pilotage de la file d'attente.
trigger: /formateur-dashboard
---

# 🧑‍🏫 WORKFLOW : PORTAIL FORMATEUR & FILE D'ATTENTE

## Commandes Déclencheuses
*   `/formateur-dashboard` : Accès et supervision de la file d'attente d'une session.
*   `/formateur-reorder` : Réorganisation de l'ordre d'attente en glisser-déposer.

## Guide Fonctionnel et Technique

### 1. Sélection de Session Active
*   Le formateur accède à l'URI `/formateur/sessions`.
*   **Service** : `SessionService->getSessionsWithStatusUpdate()` met à jour les statuts en fonction de l'heure courante (planifiée, en cours, terminée) et retourne les sessions actives.
*   L'UI affiche le bouton d'accès au dashboard uniquement si la session est active (`en cours` ou `planifiée`).

### 2. Supervision et Appel du Candidat (`/formateur-dashboard`)
*   **Contrôleur** : `FormateurController@dashboard`.
*   **Services** : `TicketService->getLiveQueue()`, `QueueService->callNextCandidat()`.
*   **Bouton Appel Suivant** :
    *   Ferme transactionnellement le ticket en cours en le passant à `'terminée'`.
    *   Récupère le premier ticket dans l'état `'en attente'` (trié par numéro d'ordre ascendant) et le passe dans l'état `'en cours'`.
    *   Notifie les clients connectés via le rafraîchissement d'interface.

### 3. Réorganisation en Glisser-Déposer (`/formateur-reorder`)
*   **JS Client** : `dashboardManager.js` initialise `Sortable.js` sur la liste des tickets en attente.
*   Lors du déplacement d'une ligne, une requête Axios POST contenant le nouvel ordre des identifiants de tickets est envoyée à `/formateur/reorder/{session}`.
*   **Service** : `QueueService->reorderQueue()` met à jour séquentiellement les `numeroOrdre` en base de données de 1 à N dans une transaction sécurisée.
