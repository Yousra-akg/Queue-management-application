<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['candidat_id', 'entretien_id', 'formateur_id', 'salle_id', 'codeUnique', 'numeroOrdre', 'statut', 'heureArrivee', 'heureAppel', 'heureFin'];

    public function candidat() {
        return $this->belongsTo(Candidat::class);
    }

    public function entretien() {
        return $this->belongsTo(Entretien::class);
    }

    public function salle() {
        return $this->belongsTo(Salle::class);
    }

    public function formateur() {
        return $this->belongsTo(User::class, 'formateur_id');
    }
}
