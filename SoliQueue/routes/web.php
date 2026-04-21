<?php

use App\Http\Controllers\CandidatAuthController;
use App\Http\Controllers\CandidatPortalController;
use Illuminate\Support\Facades\Route;

// Flux Public Candidat - Authentification
Route::get('/', [CandidatAuthController::class, 'index'])->name('login');
Route::post('/login', [CandidatAuthController::class, 'login'])->name('login.post');
Route::post('/logout', [CandidatAuthController::class, 'logout'])->name('logout');

// Flux Protégé Candidat
Route::middleware(['candidat.auth'])->group(function () {
    Route::get('/bienvenue', [CandidatPortalController::class, 'bienvenue'])->name('candidat.bienvenue');
    Route::get('/ticket-details', [CandidatPortalController::class, 'ticketDetails'])->name('candidat.ticket-details');
    Route::post('/mark-presence', [CandidatPortalController::class, 'markPresence'])->name('candidat.mark-presence');
});

