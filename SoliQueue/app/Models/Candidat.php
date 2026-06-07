<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Candidat extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['entretien_id', 'cin', 'nom', 'prenom', 'photo', 'scoreQCM', 'is_present'];

    protected $casts = [
        'is_present' => 'boolean',
    ];


    public function entretien() {
        return $this->belongsTo(Entretien::class);
    }

    public function ticket() {
        return $this->hasOne(Ticket::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}
