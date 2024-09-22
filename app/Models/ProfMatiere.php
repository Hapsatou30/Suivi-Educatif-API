<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfMatiere extends Model
{
    use HasFactory;
    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec professeurs
    public function professeur()
    {
        return $this->belongsTo(Professeur::class,'professeur_id');
    }

    //relation avec matieres
    public function matiere()
    {
        return $this->belongsTo(Matiere::class,'matiere_id');
    }

    //relation avec annee_class
    public function anneeClasses()
    {
        return $this->belongsToMany(AnneeClasse::class, 'classe_profs','annee_classe_id',  'prof_mat_id')
        ->withPivot('id','annee_classe_id',  'prof_mat_id')
        ->withTimestamps();
    }



}
