<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MobileCandidateController;
use App\Http\Controllers\MobileAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Les routes de l'application frontend.
|
*/

Route::get('/', [MobileCandidateController::class, 'showGenerationTicket'])->name('mobile.home');
Route::post('/generate-ticket', [MobileCandidateController::class, 'generateTicket'])->name('mobile.generate');

Route::get('/portal', [MobileCandidateController::class, 'showPortal'])->name('mobile.portal');
Route::post('/validate-presence', [MobileCandidateController::class, 'validatePresence'])->name('mobile.validate');
Route::get('/live-queue', [MobileCandidateController::class, 'getQueueData'])->name('mobile.queue');

Route::get('/admin', [MobileAdminController::class, 'dashboard'])->name('mobile.admin');
