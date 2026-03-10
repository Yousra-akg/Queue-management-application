<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $fillable = ['candidat_id', 'titre', 'message', 'dateEnvoi', 'estLu'];

    public function candidat() {
        return $this->belongsTo(Candidat::class);
    }
}
