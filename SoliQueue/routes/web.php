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

// Admin Auth Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SessionManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/affectations', [SessionManagementController::class, 'affectations'])->name('affectations');
    
    // Sessions CRUD
    Route::get('/sessions', [SessionManagementController::class, 'sessions'])->name('sessions.index');
    Route::post('/sessions', [SessionManagementController::class, 'storeSession'])->name('sessions.store');
    Route::put('/sessions/{session}', [SessionManagementController::class, 'updateSession'])->name('sessions.update');
    Route::delete('/sessions/{session}', [SessionManagementController::class, 'destroySession'])->name('sessions.destroy');
    
    // Candidats CRUD
    Route::get('/candidats', [SessionManagementController::class, 'candidats'])->name('candidats.index');
    Route::post('/candidates', [SessionManagementController::class, 'storeCandidate'])->name('candidates.store');
    Route::put('/candidats/{candidat}', [SessionManagementController::class, 'updateCandidate'])->name('candidats.update');
    Route::delete('/candidats/{candidat}', [SessionManagementController::class, 'destroyCandidate'])->name('candidats.destroy');
    
    // Actions rapides
    Route::post('/candidates/{candidate}/unassign', [SessionManagementController::class, 'unassignCandidate'])->name('candidates.unassign');
    Route::post('/sessions/{session}/assign', [SessionManagementController::class, 'assignCandidates'])->name('sessions.assign');
    
    // Formateurs CRUD
    Route::get('/formateurs', [FormateurManagementController::class, 'index'])->name('formateurs.index');
    Route::post('/formateurs', [FormateurManagementController::class, 'store'])->name('formateurs.store');
    Route::put('/formateurs/{formateur}', [FormateurManagementController::class, 'update'])->name('formateurs.update');
    Route::delete('/formateurs/{formateur}', [FormateurManagementController::class, 'destroy'])->name('formateurs.destroy');
});

// Portail Formateur
Route::prefix('formateur')->name('formateur.')->group(function () {
    Route::get('/login', [FormateurController::class, 'showLogin'])->name('login');
    Route::post('/login', [FormateurController::class, 'login']);

    Route::middleware(['auth:web'])->group(function () {
        Route::get('/sessions', [FormateurController::class, 'selectionSession'])->name('sessions');
        Route::get('/dashboard/{session}', [FormateurController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [FormateurController::class, 'logout'])->name('logout');
        
        // Actions AJAX
        Route::post('/status/{ticket}', [FormateurController::class, 'updateTicketStatus'])->name('update-status');
        Route::post('/reorder/{session}', [FormateurController::class, 'updateTicketOrder'])->name('reorder');
    });
});
