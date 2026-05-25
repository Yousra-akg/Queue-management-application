<?php

use App\Http\Controllers\CandidatPortalController;
use App\Http\Controllers\Admin\SessionManagementController;
use App\Http\Controllers\Admin\FormateurManagementController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Formateur\FormateurController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Authentification Laravel UI : Page de connexion à la racine
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Flux Protégé Candidat
Route::middleware(['auth'])->group(function () {
    Route::get('/bienvenue', [CandidatPortalController::class, 'bienvenue'])->name('candidat.bienvenue');
    Route::get('/ticket-details', [CandidatPortalController::class, 'ticketDetails'])->name('candidat.ticket-details');
    Route::post('/mark-presence', [CandidatPortalController::class, 'markPresence'])->name('candidat.mark-presence');
    Route::get('/queue-status', [CandidatPortalController::class, 'getQueueStatus'])->name('candidat.queue-status');
});


