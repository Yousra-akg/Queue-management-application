<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

$user = User::firstOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'nom' => 'admin',
        'password' => Hash::make('admin123')
    ]
);

// S'assurer que le rôle existe
$role = Role::firstOrCreate(['name' => 'admin']);

$user->assignRole('admin');
echo "Admin créé avec succès !";
