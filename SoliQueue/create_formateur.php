<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::firstOrCreate(
    ['email' => 'formateur@gmail.com'],
    [
        'nom' => 'Nouveau Formateur',
        'password' => Hash::make('formateur123')
    ]
);

$user->syncRoles('formateur');
echo "Formateur créé avec succès.\n";
