---
description: Guide d'installation de l'environnement SoliQueue, des dépendances et du seeding.
trigger: /install-soliqueue
---

# 🚀 INSTALLATION & INITIALISATION DE SOLIQUEUE

## Commandes Déclencheuses
*   `/install-soliqueue` : Lance les procédures d'initialisation de l'environnement Laravel et MySQL.

## Étapes d'Initialisation

### 1. Clonage et Dépendances
*   **Composer** : Installer les dépendances backend :
    ```bash
    composer install
    ```
*   **NPM** : Installer les dépendances frontend (Tailwind CSS, Alpine.js, Sortable.js, Axios, SweetAlert2) :
    ```bash
    npm install
    ```

### 2. Configuration d'Environnement
*   Copier le fichier `.env.example` en `.env`.
*   Configurer la connexion de base de données **MySQL** :
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=SoliQueue
    DB_USERNAME=votre_utilisateur
    DB_PASSWORD=votre_mot_de_passe
    ```

### 3. Base de données, Rôles et Seeding
*   Exécuter les migrations de base de données et le seeder de départ (crée les rôles `admin`, `formateur` et synchronise les permissions) :
    ```bash
    php artisan migrate:fresh --seed
    ```
*   Ce seeder génère également les comptes d'accès par défaut (ex: Administrateur principal, Formateur Omar Azami, etc.).

### 4. Serveur Local
*   Démarrer le serveur de développement Laravel :
    ```bash
    php artisan serve
    ```
*   Démarrer le compilateur d'assets Vite en arrière-plan :
    ```bash
    npm run dev
    ```
