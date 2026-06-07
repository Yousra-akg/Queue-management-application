<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['nom', 'email', 'password'];



    // Un utilisateur (Admin ou Formateur via son compte User) gère des entretiens
    public function entretiens() {
        return $this->hasMany(Entretien::class);
    }
}