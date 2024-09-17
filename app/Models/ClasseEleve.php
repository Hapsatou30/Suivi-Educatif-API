<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasseEleve extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec annee_classe
    public function anneeClasse()
    {
        return $this->belongsTo(AnneeClasse::class);
    }
    //relation avec éleve
    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    //relation avec presence
    public function presence()
    {
        return $this->hasMany(Presence::class);
    }

    //relation avec bulletin
    public function bulletins()
    {
        return $this->hasMany(Bulletin::class);
    }

}
