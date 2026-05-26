---
description: Guide du portail candidat, connexion par CIN, confirmation de présence, et ticket d'attente en temps réel.
trigger: /candidat-ticket
---

# 🎟️ WORKFLOW : PORTAIL CANDIDAT & TICKET TEMPS RÉEL

## Commandes Déclencheuses
*   `/candidat-ticket` : Processus de suivi de ticket, polling dynamique et confirmation de présence.

## Parcours Utilisateur du Candidat

### Etape 1 : Authentification par CIN
*   Le candidat saisit son CIN sur la page d'accueil `/`.
*   **Service** : `CandidatService->loginByCin()` valide l'existence du candidat et renvoie ses données.
*   **Sécurité** : Si le candidat existe mais n'a pas encore été affecté à une session d'entretien par l'administrateur, un message d'erreur clair est affiché.

### Etape 2 : Page de Bienvenue
*   Affiche les informations personnelles du candidat et l'invite à se diriger vers sa salle d'entretien attitrée.

### Etape 3 : Validation de la Présence Physique
*   À l'entrée de la salle, le candidat doit saisir le code de présence affiché par le formateur.
*   **Service** : `CandidatService->validateAndConfirmPresence()`.
*   Si le code correspond (les espaces saisis par l'utilisateur sont retirés automatiquement), le candidat est marqué comme `'présent'` et son ticket d'attente passe automatiquement de `'en attente'` à `'en cours'` (ou reste en file active).
*   Cette validation débloque l'affichage en direct de la file d'attente.

### Etape 4 : Suivi en Temps Réel et Polling
*   **JS Client** : `ticketManager.js` initie une boucle de rafraîchissement asynchrone (`setInterval`) toutes les 5 secondes en interrogeant `/queue-status`.
*   **Service** : `TicketService->getLiveQueue()` renvoie la file à jour.
*   **Composants de l'interface** :
    *   **Position en temps réel** : Affiche combien de candidats restent avant le passage de l'utilisateur.
    *   **Changement visuel** : Si le statut du ticket passe à `'en cours'`, l'interface s'anime avec une pastille clignotante et invite le candidat à entrer dans la salle.
    *   **Compte à rebours** : Compte à rebours dynamique basé sur le timestamp de début de session.
