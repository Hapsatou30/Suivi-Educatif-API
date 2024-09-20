<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasseProf extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec annee_classe
    public function anneeClasse()
    {
        return $this->belongsTo(AnneeClasse::class,'annee_classe_id');
    }

    //relation avec prof_matiere
    public function profMatiere()
    {
        return $this->belongsTo(ProfMatiere::class,'prof_mat_id');
    }
    
    
    //relation avec évaluation
    public function evaluations()
    {
        return $this->hasMany(Evaluations::class);
    }

    //relation avec cahier de texte
    public function cahiersDeTextes()
    {
        return $this->hasMany(CahierTexte::class);
    }

    //relation avec horaire
    public function horaires()
    {
        return $this->hasMany(Horaire::class);
    }
}
