---
trigger: always_on
type: rule
id: soliqueue-master-instructions
---

# DIRECTIVES MAÎTRESSES - SOLIQUEUE

## Architecture du Projet

### 1. Organisation en Couches (Service Pattern)
*   **Contrôleurs Fins** : Les contrôleurs dans `app/Http/Controllers/` ne doivent contenir aucune logique métier complexe, écriture directe ou transaction. Ils valident les requêtes HTTP (via standard validation) et délèguent l'exécution aux services appropriés.
*   **Services Riches** : Toute la logique métier réside exclusivement dans la couche `app/Services/`. Chaque module principal a son service dédié (ex: `CandidatService`, `SessionService`, `QueueService`, `TicketService`, `FormateurService`).
*   **Transactions de Données** : Les écritures multiples en base de données doivent être englobées dans une transaction (`DB::transaction(...)`) pour assurer la cohérence et l'intégrité (notamment lors de la création de formateur, l'assignation de candidat ou la validation de présence).

### 2. Accès aux Données via Eloquent uniquement
*   Aucune requête SQL brute ni utilisation de `DB::table()` direct pour les écritures.
*   Toujours utiliser les modèles Eloquent (`User`, `Formateur`, `Session`, `Candidat`, `Ticket`) et leurs relations définies.

### 3. Gestion de l'Authentification et Multi-Guards
*   **Guard par défaut** : Le guard par défaut est configuré sur `candidat` pour l'authentification sans mot de passe des candidats (connexion par CIN).
*   **Guard Web** : Le guard `web` (basé sur la table `users`) est utilisé explicitement pour l'accès sécurisé des Formateurs et des Administrateurs.
*   **Authentification et Mots de Passe** : Le modèle `User` utilise le cast `'password' => 'hashed'`. Le mot de passe est haché via `Hash::make` uniquement lors de la création ou modification via `FormateurService`.

### 4. Norme Linguistique Obligatoire
*   **Tout en Français** : Le code (variables personnalisées, noms de méthodes métier, commentaires, documentations, messages d'erreurs et étiquettes UI) doit être rédigé exclusivement en Français pour préserver l'identité de l'application SoliQueue.
*   Respecter les modèles et tables existants en base de données.

### 5. Composants UI et Gestion de la File d'Attente
*   **Alpine.js Externe & Tailwind CSS** : Le projet repose sur Tailwind CSS et Alpine.js pour les interactions. Tout le code JS interactif (gestionnaires Alpine) doit être stocké dans des fichiers managers propres dans `resources/js/components/` répartis par dossiers d'acteurs.
*   **Drag-and-Drop** : La file d'attente du formateur est réorganisée par glisser-déposer asynchrone (interfacée avec `Sortable.js` et soumise au contrôleur via Axios).
*   **Toasts et Modales** : Les retours d'actions (succès de création, erreur de capacité maximale, retrait bloqué) doivent déclencher des notifications animées ou des modales asynchrones via Alpine.js.

### 6. Gestion des Fichiers et Photos
*   Toutes les photos téléchargées (photos de profil des candidats) doivent être stockées de manière sécurisée via le système de stockage de fichiers de Laravel (`Storage::disk('public')->store('candidats')`) et supprimées du disque physique lors de la suppression du candidat.
