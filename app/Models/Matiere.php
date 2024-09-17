<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matiere extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec professeurs
    public function professeurs()
    {
        return $this->belongsToMany(Professeur::class, 'prof_matieres')
        ->withPivot('id','professeur_id', 'matiere_id')
        ->withTimestamps();
    }
}
