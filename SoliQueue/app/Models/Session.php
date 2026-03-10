<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model {
    protected $fillable = ['user_id', 'nom', 'dateEntretien', 'heureDebut', 'heureFin', 'capaciteMax', 'codePresence', 'statut'];

    public function user() { 
        return $this->belongsTo(User::class);
    }

    public function candidats() {
        return $this->hasMany(Candidat::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}
