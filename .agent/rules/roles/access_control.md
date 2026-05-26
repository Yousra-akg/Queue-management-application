---
trigger: always_on
type: rule
id: soliqueue-access-control
---

# CONTRÔLE D'ACCÈS ET RÔLES (RBAC) - SOLIQUEUE

## Matrice des Rôles et Permissions

SoliQueue exploite `Spatie Laravel Permission` pour sécuriser les actions sensibles des acteurs.

| Rôle | Périmètre d'Accès | Permissions Associées |
|---|---|---|
| **admin** | Accès complet à la partie Web Administration (KPIs, CRUD formateurs, CRUD candidats, CRUD sessions, affectations/désaffectations). Accès possible au panel formateur. | `manage_queue` |
| **formateur** | Accès exclusif à la partie Portail Formateur (sélection de session, dashboard de file d'attente, rappel candidat, mise à jour de statut, réordonnancement). Aucun accès à l'administration. | `manage_queue` |
| **candidat** | Accès exclusif au Portail Candidat via authentification CIN (bienvenue, ticket, position dans la file d'attente, confirmation de présence). | Pas de permission Spatie (limité par guard candidat) |

## Routage et Protection dans `routes/web.php`

### 1. Protection Administrateur
L'espace d'administration utilise le middleware personnalisé `admin` qui vérifie que l'utilisateur connecté possède le rôle Spatie `'admin'` :
```php
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SessionManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/affectations', [SessionManagementController::class, 'affectations'])->name('affectations');
    // CRUD Sessions, Candidats, Formateurs...
});
```

### 2. Protection Formateur
L'espace formateur utilise le guard standard `auth:web` pour authentifier les utilisateurs connectés. Les actions critiques sont protégées par la permission `manage_queue` :
```php
Route::middleware(['auth:web'])->group(function () {
    Route::get('/sessions', [FormateurController::class, 'selectionSession'])->name('sessions');
    Route::get('/dashboard/{session}', [FormateurController::class, 'dashboard'])->name('dashboard');
    
    // Actions AJAX soumises à permission
    Route::post('/status/{ticket}', [FormateurController::class, 'updateTicketStatus'])->name('update-status');
    Route::post('/reorder/{session}', [FormateurController::class, 'updateTicketOrder'])->name('reorder');
});
```

## Vérification de sécurité lors de la connexion formateur/admin
Le `FormateurController` autorise l'accès aux sessions uniquement si l'utilisateur possède l'un des deux rôles :
```php
if ($user->hasRole('formateur') || $user->hasRole('admin')) {
    $request->session()->regenerate();
    return redirect()->route('formateur.sessions');
}
```
Si l'utilisateur n'a aucun de ces rôles (ou s'il s'agit d'un simple candidat), la connexion est bloquée.
