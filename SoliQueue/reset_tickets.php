<?php
App\Models\Ticket::where('statut', 'en cours')->update(['statut' => 'en attente']);
echo "Reset tickets to 'en attente'";
