<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$notifications = \App\Models\Notification::where('candidat_id', 27)->get()->toArray();
echo "NOTIFICATIONS CANDIDAT 27:\n";
print_r($notifications);

$ticket = \App\Models\Ticket::where('candidat_id', 27)->first();
echo "\nTICKET CANDIDAT 27:\n";
print_r($ticket ? $ticket->toArray() : 'Aucun ticket');
