<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model {
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

    public function formateurs() {
        return $this->belongsToMany(User::class, 'entretien_formateur_salle', 'entretien_id', 'formateur_id')
                    ->withPivot('salle_id')
                    ->withTimestamps();
    }

    public function updateStatusBasedOnTime() {
        $now = now();
        $dateEntretien = \Carbon\Carbon::parse($this->dateEntretien);
        $start = \Carbon\Carbon::parse($this->dateEntretien . ' ' . $this->heureDebut);
        $end = \Carbon\Carbon::parse($this->dateEntretien . ' ' . $this->heureFin);

        $newStatut = $this->statut;

        if ($this->statut === 'annulée') return;

        if ($now->greaterThanOrEqualTo($end)) {
            $newStatut = 'terminée';
        } elseif ($now->greaterThanOrEqualTo($start)) {
            $newStatut = 'en cours';
        } else {
            $newStatut = 'planifiée';
        }

        if ($newStatut !== $this->statut) {
            $this->update(['statut' => $newStatut]);
        }
    }
}
