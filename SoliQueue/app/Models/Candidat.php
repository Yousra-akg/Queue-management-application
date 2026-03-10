<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidat extends Model {
    protected $fillable = ['session_id', 'nom', 'prenom', 'scoreQCM'];

    public function session() {
        return $this->belongsTo(Session::class);
    }

    public function ticket() {
        return $this->hasOne(Ticket::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}
