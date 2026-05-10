<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Session::all() as $s) {
    echo "ID: " . $s->id . " | Nom: " . $s->nom . PHP_EOL;
}
