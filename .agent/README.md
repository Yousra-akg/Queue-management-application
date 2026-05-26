# SoliQueue Agent Configuration

Ce dossier `.agent` contient la configuration complète pour le développement, la maintenance et l'évolution de la plateforme **SoliQueue** — un système de gestion en temps réel de files d'attente pour les entretiens des candidats à Solicode.

## Structure du Dossier

```
.agent/
├── README.md                 # Présentation globale, rôles et commandes de l'agent
├── rules/                    # Directives architecturales et normes du projet
│   ├── system/
│   │   └── master_instructions.md  # Règles globales, service pattern, français obligatoire
│   ├── data/
│   │   ├── soliqueue_schema.md     # Schéma de base de données et relations Eloquent
│   │   └── service_layer.md        # Conventions de la couche Service (Business logic)
│   ├── roles/
│   │   └── access_control.md       # Matrice de sécurité RBAC (Spatie Laravel Permission)
│   └── visual/
│       └── identity.md             # Charte graphique (Tailwind v4, HSL colors, Outfits)
├── skills/                   # Compétences spécialisées de l'agent
│   ├── soliqueue-architect/  # Gestion des migrations, modèles et intégrité de la DB
│   ├── soliqueue-builder/    # Logique métier, services, tickets et files d'attente
│   └── soliqueue-developer/  # Intégration Blade, modularisation Alpine.js et CSS
└── workflows/                # Scénarios et guides pas à pas de développement
    ├── shared/               # Processus communs (Installation, Connexion)
    ├── admin/                # Gestion des sessions, candidats, formateurs et affectations
    ├── formateur/            # Sélection de session, dashboard de file, rappel et statut
    └── candidat/             # Authentification CIN, présence et ticket dynamique
```

## Rôles Clés dans SoliQueue

*   **Candidat** : S'authentifie avec son CIN, valide sa présence via le code secret de la session, génère son ticket d'attente et suit sa position dans la file d'attente en temps réel.
*   **Formateur** : Sélectionne sa session active, supervise la file d'attente, appelle le candidat suivant, réorganise la file d'attente en glisser-déposer (drag-and-drop) et met à jour les statuts de passage.
*   **Administrateur** : Pilote le tableau de bord global, gère les formateurs (CRUD), gère les candidats (CRUD), configure les sessions (CRUD) et affecte/retire les candidats aux différentes sessions d'entretien.

## Commandes Rapides de l'Agent

| Commande | Description |
|---|---|
| `/install-soliqueue` | Initialisation de l'environnement Laravel 11.x, base de données MySQL et dépendances |
| `/auth-configuration` | Paramétrage des guards (`candidat` et `web`) et sécurité Spatie Permissions |
| `/admin-dashboard` | Métriques clés de l'administration et flux d'activités en direct |
| `/admin-sessions` | CRUD des sessions d'entretiens et calcul des statuts temporels |
| `/admin-candidats` | CRUD des candidats, gestion des photos et scores QCM |
| `/admin-formateurs` | CRUD des formateurs (hachage, transactions et Spatie Roles) |
| `/admin-affectations` | Panel d'affectation multiple des candidats et génération automatique de tickets |
| `/formateur-dashboard` | Suivi et pilotage de la file d'attente d'une session par le formateur |
| `/formateur-reorder` | Tri asynchrone des numéros d'ordre en glisser-déposer (Alpine.js) |
| `/candidat-ticket` | Validation de code secret, statut de présence et compte à rebours dynamique |

## Cadre Académique

> **Projet SoliQueue** — Solicode Tanger  
> **Auteur :** Yousra Akajou  
> **Encadrant :** M. ESSARRAJ Fouad  
> **Session :** 2025/2026  
>
> ---
> **Projet :** SoliQueue  
> **Version de l'Agent :** 1.0.0 (Unique et Personnalisée)
