---
name: soliqueue-architect
description: Gestion de l'architecture de la base de données, des migrations, des modèles Eloquent et des relations de SoliQueue.
---

# 🏗️ COMPÉTENCE : SOLIQUEUE ARCHITECT

## Rôle et Domaine d'Action
Cette compétence gère le schéma relationnel et la configuration de persistance des données pour le projet **SoliQueue**. Elle assure que les clés étrangères, les types de colonnes et les indexes de recherche (comme le CIN unique ou le code interne du formateur) sont rigoureusement définis.

## Les Modèles et Leurs Relations

### 1. Modèle User
```php
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['nom', 'email', 'password'];

    public function formateur() { return $this->hasOne(Formateur::class); }
    public function sessions() { return $this->hasMany(Session::class); }
}
```

### 2. Modèle Formateur
```php
class Formateur extends Model
{
    protected $fillable = ['user_id', 'codeInterne', 'specialite'];

    public function user() { return $this->belongsTo(User::class); }
}
```

### 3. Modèle Session
```php
class Session extends Model
{
    protected $fillable = ['user_id', 'nom', 'dateEntretien', 'heureDebut', 'heureFin', 'capaciteMax', 'codePresence', 'statut'];

    public function user() { return $this->belongsTo(User::class); }
    public function candidats() { return $this->hasMany(Candidat::class); }
    public function tickets() { return $this->hasMany(Ticket::class); }

    public function updateStatusBasedOnTime()
    {
        $now = now();
        $start = \Carbon\Carbon::parse($this->dateEntretien . ' ' . $this->heureDebut);
        $end = \Carbon\Carbon::parse($this->dateEntretien . ' ' . $this->heureFin);

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
```

### 4. Modèle Candidat
```php
class Candidat extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['session_id', 'cin', 'nom', 'prenom', 'photo', 'scoreQCM', 'is_present'];
    protected $casts = ['is_present' => 'boolean'];

    public function session() { return $this->belongsTo(Session::class); }
    public function ticket() { return $this->hasOne(Ticket::class); }
}
```

### 5. Modèle Ticket
```php
class Ticket extends Model
{
    protected $fillable = ['candidat_id', 'session_id', 'codeUnique', 'numeroOrdre', 'statut', 'heureArrivee'];
    protected $casts = ['heureArrivee' => 'datetime'];

    public function candidat() { return $this->belongsTo(Candidat::class); }
    public function session() { return $this->belongsTo(Session::class); }
}
```

## Directives d'Intégrité de Données
*   **Contraintes Référentielles** : Cascade delete sur le profil Formateur lors de la suppression de l'User associé.
*   **Sécurisation par Assignation Massive** : Toujours utiliser `$fillable` pour spécifier précisément les colonnes modifiables, empêchant l'injection de champs comme les rôles ou l'état de présence lors de requêtes publiques.
