---
description: Guide de configuration et d'interception de l'authentification multi-guards.
trigger: /auth-configuration
---

# 🔐 PROCESSUS D'AUTHENTIFICATION ET MULTI-GUARDS

## Commandes Déclencheuses
*   `/auth-configuration` : Analyse et mise en œuvre des guards et restrictions d'accès.

## Architecture de Connexion

SoliQueue sépare hermétiquement les acteurs à l'aide de deux Guards distincts :

1.  **Guard `candidat` (Par défaut)** :
    *   **Mécanisme** : Connexion par CIN uniquement (sans mot de passe).
    *   **Contrôleur** : `App\Http\Controllers\Auth\LoginController`.
    *   **Service** : `CandidatService->loginByCin()`.
    *   **Middleware** : `auth` (redirige vers `/` en cas d'accès non autorisé).
    *   **Redirection** : Redirige vers `/bienvenue`.

2.  **Guard `web` (Formateurs et Administrateurs)** :
    *   **Mécanisme** : Connexion standard par e-mail et mot de passe.
    *   **Contrôleurs** : `AdminLoginController` (Admin) et `FormateurController` (Formateurs).
    *   **Middlewares** :
        *   `auth:web` pour sécuriser l'accès aux sessions formateurs.
        *   `admin` (middleware personnalisé) pour restreindre l'accès à la zone d'administration aux seuls comptes possédant le rôle Spatie `'admin'`.
    *   **Vérification de Garde** : `FormateurController@showLogin` vérifie explicitement `Auth::guard('web')->check()` pour éviter toute interférence avec une session candidat ouverte sur le même navigateur.

## Redirections d'Invités (Guests)
Configurées dynamiquement dans `bootstrap/app.php` selon le préfixe de l'URI consultée :
```php
$middleware->redirectGuestsTo(function ($request) {
    if ($request->is('admin') || $request->is('admin/*')) {
        return route('admin.login');
    }
    if ($request->is('formateur') || $request->is('formateur/*')) {
        return route('formateur.login');
    }
    return route('login');
});
```
