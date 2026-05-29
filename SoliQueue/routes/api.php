<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EtudiantApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\SessionApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici sont inscrites toutes les routes API pour l'application Mobile.
| Elles sont préfixées par "/api" via le routeur Laravel de base.
|
*/

Route::prefix('mobile')->group(function () {
    
    // 0. Authentification du candidat par son CIN
    Route::post('/login', [EtudiantApiController::class, 'login']);
    
    // 1. Retourne un étudiant (candidat) aléatoire
    Route::get('/random-student', [EtudiantApiController::class, 'getRandomStudent']);

    // 2. Génère un ticket pour un étudiant spécifique
    Route::post('/tickets/generate', [TicketApiController::class, 'generate']);

    // 3. Statut d'une session et son chrono
    Route::get('/sessions/{id}/status', [SessionApiController::class, 'getStatus']);
    Route::get('/sessions/{id}/queue', [TicketApiController::class, 'getQueue']);

    // 4. Valide la présence avec un code
    Route::post('/tickets/validate-presence', [TicketApiController::class, 'validatePresence']);

    // 5. Dashboard Admin : Statistiques des tickets
    Route::get('/admin/dashboard', [SessionApiController::class, 'getDashboardStats']);

});
