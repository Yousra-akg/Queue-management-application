<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/live-queue', 'GET', [
    'session_id' => 15,
    'candidate_id' => 20
]);

// Let's directly call MobileCandidateController
$controller = app(\App\Http\Controllers\MobileCandidateController::class);
$response = $controller->getQueueData($request);

echo "MOBILE RESPONSE:\n";
echo $response->getContent();
