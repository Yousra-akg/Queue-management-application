---
trigger: always_on
type: rule
id: soliqueue-schema
---

# SCHÉMA ET RELATIONS DE BASE DE DONNÉES - SOLIQUEUE

## Diagramme Relationnel Logique

```
User (1) ◄─────────► (1) Formateur

User (1) ───► (N) Session (Créateur de la session)

Session (1) ───► (N) Candidat
Session (1) ───► (N) Ticket

Candidat (1) ◄─────────► (1) Ticket
```

## Règles Clés et Contraintes de Données

1.  **Héritage de Table Formateur (One-to-One) :**
    *   Le modèle `Formateur` étend les informations de l'utilisateur. Il possède une relation One-to-One avec `users(id)` via `user_id` en clé étrangère (suppression en cascade).
    *   Colonnes : `user_id`, `codeInterne` (Unique), `specialite`.

2.  **Modèle Session :**
    *   Une session appartient à l'administrateur/utilisateur qui l'a créée (`user_id`).
    *   Colonnes : `nom` (ex: "Session Java"), `dateEntretien` (Date), `heureDebut` (Time), `heureFin` (Time), `capaciteMax` (Integer), `codePresence` (String), `statut` (Enum: 'planifiée', 'en cours', 'terminée', 'annulée').
    *   La méthode `updateStatusBasedOnTime()` met à jour le statut de la session dynamiquement à la volée selon l'heure courante.

3.  **Modèle Candidat :**
    *   Un candidat s'authentifie avec son `cin` (Unique). Il peut être affecté à une unique `session_id` (Nullable).
    *   Colonnes : `nom`, `prenom`, `cin` (Unique), `scoreQCM` (Decimal), `photo` (String, Path), `session_id` (Clé étrangère, Nullable), `is_present` (Boolean, défaut: false).

4.  **Modèle Ticket :**
    *   Un ticket est lié de manière unique à un candidat (`candidat_id`) pour une session donnée (`session_id`).
    *   Colonnes : `candidat_id` (Clé étrangère), `session_id` (Clé étrangère), `codeUnique` (String, ex: "SOLI-01"), `numeroOrdre` (Integer, incrémenté), `statut` (Enum: 'en attente', 'en cours', 'terminée', 'absent'), `heureArrivee` (DateTime).

## Ordre Strict des Migrations

1.  `create_users_table` (Nom, email, password)
2.  `create_password_reset_tokens_table`
3.  `create_sessions_table` (Date, heures, capacité, code et statut)
4.  `create_candidats_table` (CIN, score QCM, photo, is_present, lié à une session)
5.  `create_tickets_table` (Code unique, numéro d'ordre, statut, heure d'arrivée, lié à un candidat et session)
6.  `create_formateurs_table` (Spécialité, code interne, lié à un utilisateur)
7.  `create_permission_tables` (Configuration Spatie Laravel-Permission)
