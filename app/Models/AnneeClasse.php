<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnneeClasse extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec annee scolaire
    public function annee()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
    //relation avec classe
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    //relation avec prof_matiere
    public function profMatieres()
    {
        return $this->belongsToMany(ProfMatiere::class, 'classe_profs','annee_classe_id', 'prof_mat_id')
        ->withPivot('id', 'annee_classe_id', 'prof_mat_id')
        ->withTimestamps();
    }

    //relation avec eleves
    public function eleves()
    {
        return $this->belongsToMany(Eleve::class, 'classe_eleves')
        ->withPivot('id', 'annee_classe_id', 'eleve_id')
        ->withTimestamps();
    }
  
}
