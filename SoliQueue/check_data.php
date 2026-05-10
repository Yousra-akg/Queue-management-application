<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Candidat;
use App\Models\Ticket;
use App\Models\Session;

$candidats = Candidat::where('nom', 'LIKE', '%Mansouri%')->with('session', 'ticket')->get();

foreach ($candidats as $c) {
    echo "Nom: " . $c->nom . PHP_EOL;
    echo "ID: " . $c->id . PHP_EOL;
    echo "Session ID: " . $c->session_id . PHP_EOL;
    echo "Code Presence (Expected): " . ($c->session ? $c->session->codePresence : 'N/A') . PHP_EOL;
    echo "Is Present: " . ($c->is_present ? 'Yes' : 'No') . PHP_EOL;
    echo "Ticket Status: " . ($c->ticket ? $c->ticket->statut : 'No Ticket') . PHP_EOL;
    echo "-------------------" . PHP_EOL;
}
