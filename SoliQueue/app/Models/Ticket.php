<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['candidat_id', 'session_id', 'codeUnique', 'numeroOrdre', 'statut', 'heureArrivee'];

    public function candidat() {
        return $this->belongsTo(Candidat::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }
}
