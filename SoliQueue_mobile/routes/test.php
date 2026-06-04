<?php
use Illuminate\Support\Facades\Route;

Route::get('/test-queue', function (\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\MobileCandidateController::class);
    $req = \Illuminate\Http\Request::create('/live-queue', 'GET', [
        'session_id' => 15,
        'candidate_id' => 20
    ]);
    return $controller->getQueueData($req);
});
