---
description: Guide pour l'espace administration, CRUD candidats/sessions/formateurs, et panel d'affectations.
trigger: /admin-dashboard
---

# 👑 WORKFLOW : PORTAIL D'ADMINISTRATION

## Commandes Déclencheuses
*   `/admin-dashboard` : Statistiques d'administration et flux d'activités récentes.
*   `/admin-sessions` : Configuration et CRUD des sessions.
*   `/admin-candidats` : CRUD des candidats et téléversement de photos.
*   `/admin-formateurs` : Création et habilitation des formateurs.
*   `/admin-affectations` : Panel d'affectations en glisser-déposer.

## Étapes de Développement et de Maintenance

### 1. Tableau de Bord Global (`/admin-dashboard`)
*   **Contrôleur** : `SessionManagementController@dashboard`.
*   **Services** : `SessionService->getDashboardStatsAdmin()`, `SessionService->getRecentActivitiesFeed()`.
*   **Contenu** : Cartes KPIs (Total Candidats, Sessions, Taux de présence) et flux d'activités en direct fusionnant les dernières créations de sessions et les affectations.

### 2. CRUD Candidats & Sessions (`/admin-candidats` et `/admin-sessions`)
*   **Candidats** : Gérés par `CandidatService`. Encodage JSON avec `->values()` sur la collection pour la compatibilité avec Alpine.js.
*   **Sessions** : Gérées par `SessionService`. Statuts mis à jour à la volée.

### 3. Gestion des Formateurs (`/admin-formateurs`)
*   **Service** : `FormateurService`.
*   **Spécificités** : Création transactionnelle de l'utilisateur lié (`User`) avec mot de passe crypté via `Hash::make()` et assignation automatique du rôle Spatie `formateur` (`$user->syncRoles(['formateur'])`).

### 4. Panel d'Affectations (`/admin-affectations`)
*   **Service** : `CandidatService->assignCandidatesToSession()` et `unassignCandidateFromSession()`.
*   **Règle de sécurité majeure** : Interdiction backend absolue de désaffecter un candidat présent d'une session dont le statut est `'terminée'`.
*   **Règle d'UI** : Masquage frontend de la croix de désaffectation sur les candidats présents dans une session terminée, pastilles animées clignotantes de présence.
