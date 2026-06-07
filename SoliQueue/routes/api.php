<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EtudiantApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\EntretienApiController;

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
    Route::get('/candidates/{id}', [EtudiantApiController::class, 'getCandidate']);
    
    // 1. Retourne un étudiant (candidat) aléatoire
    Route::get('/random-student', [EtudiantApiController::class, 'getRandomStudent']);

    // 2. Génère un ticket pour un étudiant spécifique
    Route::post('/tickets/generate', [TicketApiController::class, 'generate']);
    Route::get('/tickets/{id}', [TicketApiController::class, 'getTicket']);

    // 3. Statut d'une entretien et son chrono
    Route::get('/entretiens/{id}/status', [EntretienApiController::class, 'getStatus']);
    Route::get('/entretiens/{id}/queue', [TicketApiController::class, 'getQueue']);

    // 4. Valide la présence avec un code
    Route::post('/tickets/validate-presence', [TicketApiController::class, 'validatePresence']);

    // 5. Dashboard Admin : Statistiques des tickets
    Route::get('/admin/dashboard', [EntretienApiController::class, 'getDashboardStats']);

    // 6. Notifications
    Route::post('/notifications/{id}/read', [TicketApiController::class, 'markNotificationRead']);

});

