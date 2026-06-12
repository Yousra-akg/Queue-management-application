<?php

use App\Http\Controllers\CandidatPortalController;
use App\Http\Controllers\Admin\EntretienManagementController;
use App\Http\Controllers\Admin\FormateurManagementController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Formateur\FormateurController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChatbotController;
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
    Route::post('/notifications/{notification}/read', [CandidatPortalController::class, 'markNotificationRead'])->name('candidat.notification.read');
});

// Chatbot Route (accessible pour tous les utilisateurs web - admin, formateur, candidat)
Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chat.send');

// Admin Auth Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [EntretienManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/affectations', [EntretienManagementController::class, 'affectations'])->name('affectations');
    
    // Entretiens CRUD
    Route::get('/entretiens', [EntretienManagementController::class, 'entretiens'])->name('entretiens.index');
    Route::post('/entretiens', [EntretienManagementController::class, 'storeEntretien'])->name('entretiens.store');
    Route::put('/entretiens/{entretien}', [EntretienManagementController::class, 'updateEntretien'])->name('entretiens.update');
    Route::delete('/entretiens/{entretien}', [EntretienManagementController::class, 'destroyEntretien'])->name('entretiens.destroy');
    
    // Candidats CRUD
    Route::get('/candidats', [EntretienManagementController::class, 'candidats'])->name('candidats.index');
    Route::post('/candidates', [EntretienManagementController::class, 'storeCandidate'])->name('candidates.store');
    Route::put('/candidats/{candidat}', [EntretienManagementController::class, 'updateCandidate'])->name('candidats.update');
    Route::delete('/candidats/{candidat}', [EntretienManagementController::class, 'destroyCandidate'])->name('candidats.destroy');
    
    // Actions rapides
    Route::post('/candidates/{candidate}/unassign', [EntretienManagementController::class, 'unassignCandidate'])->name('candidates.unassign');
    Route::post('/entretiens/{entretien}/assign', [EntretienManagementController::class, 'assignCandidates'])->name('entretiens.assign');
    Route::get('/candidats/{candidat}/details', [EntretienManagementController::class, 'getCandidatDetails'])->name('candidats.details');
    
    // Formateurs CRUD
    Route::get('/formateurs', [FormateurManagementController::class, 'index'])->name('formateurs.index');
    Route::post('/formateurs', [FormateurManagementController::class, 'store'])->name('formateurs.store');
    Route::put('/formateurs/{formateur}', [FormateurManagementController::class, 'update'])->name('formateurs.update');
    Route::delete('/formateurs/{formateur}', [FormateurManagementController::class, 'destroy'])->name('formateurs.destroy');

    // Salles CRUD
    Route::get('/salles', [\App\Http\Controllers\Admin\SalleController::class, 'index'])->name('salles.index');
    Route::post('/salles', [\App\Http\Controllers\Admin\SalleController::class, 'store'])->name('salles.store');
    Route::delete('/salles/{salle}', [\App\Http\Controllers\Admin\SalleController::class, 'destroy'])->name('salles.destroy');

    // Import & Export
    Route::get('/candidats/export', [ImportExportController::class, 'exportCandidats'])->name('candidats.export');
    Route::post('/candidats/import', [ImportExportController::class, 'importCandidats'])->name('candidats.import');
    Route::get('/entretiens/export', [ImportExportController::class, 'exportEntretiens'])->name('entretiens.export');
});

// Portail Formateur
Route::prefix('formateur')->name('formateur.')->group(function () {
    Route::get('/login', [FormateurController::class, 'showLogin'])->name('login');
    Route::post('/login', [FormateurController::class, 'login']);

    Route::middleware(['auth:web'])->group(function () {
        Route::get('/entretiens', [FormateurController::class, 'selectionEntretien'])->name('entretiens');
        Route::get('/dashboard/{entretien}', [FormateurController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [FormateurController::class, 'logout'])->name('logout');
        
        // Actions AJAX
        Route::post('/status/{ticket}', [FormateurController::class, 'updateTicketStatus'])->name('update-status');
        Route::post('/reorder/{entretien}', [FormateurController::class, 'updateTicketOrder'])->name('reorder');
    });
});

