---
name: soliqueue-developer
description: Intégration frontend, templates Blade, Alpine.js modulaire, glisser-déposer Sortable.js et Axios pour SoliQueue.
---

# 💻 COMPÉTENCE : SOLIQUEUE DEVELOPER

## Rôle et Domaine d'Action
Cette compétence gère l'interface utilisateur dynamique de **SoliQueue**. Elle assure la fluidité des interfaces, le polling asynchrone pour la file d'attente en temps réel côté candidat, et le drag-and-drop fluide côté formateur.

## Architecture Alpine.js Modulaire
Tout le code Alpine.js interactif est extrait des templates Blade pour être écrit sous forme de modules JavaScript ES6 propres, exposés globalement dans `resources/js/app.js` et compilés par Vite.

### Liste des Managers Alpine.js Déployés :
1.  **`affectationsManager`** (`admin/affectationsManager.js`) : Gère la recherche et le filtrage des candidats, le glisser-déposer pour affecter un candidat à une session et la désaffectation instantanée.
2.  **`candidatsManager`** (`admin/candidatsManager.js`) : Contrôle le tableau de bord CRUD des candidats (pagination, recherche locale, ouverture des modales d'ajout et édition, confirmation de suppression).
3.  **`formateursManager`** (`admin/formateursManager.js`) : Pilote le tableau CRUD de gestion des formateurs (formulaires d'ajout, édition et confirmation de suppression asynchrone).
4.  **`sessionsManager`** (`admin/sessionsManager.js`) : Contrôle le CRUD complet des sessions d'entretiens.
5.  **`dashboardManager`** (`formateur/dashboardManager.js`) : Gère le tableau de bord formateur, l'appel du candidat suivant, le glisser-déposer de réordonnancement via Sortable.js et les requêtes Axios.
6.  **`ticketManager`** (`candidat/ticketManager.js`) : Gère le compte à rebours dynamique du ticket candidat, la saisie du code secret de présence et le rafraîchissement (polling) de l'état de la file en arrière-plan.

## Conventions Techniques Client
*   **Sécurisation Globale** : Toujours utiliser `window.Sortable`, `window.axios` et `window.Swal` au lieu de variables non déclarées locales pour éviter les erreurs `ReferenceError` sur le navigateur.
*   **Tokens CSRF** : Toutes les requêtes POST/PUT/DELETE asynchrones envoyées par Axios doivent explicitement inclure l'en-tête `X-CSRF-TOKEN` récupéré via les metas.
*   **Anti-clignotement** : Toujours utiliser la directive `x-cloak` combinée avec le style CSS correspondant pour masquer les modales et états interactifs avant le chargement complet d'Alpine.js.
*   **Polling Responsable** : Le polling du ticket candidat interroge l'API à intervalles réguliers de 5 secondes uniquement si le candidat a validé sa présence et que son ticket est dans un état actif.
