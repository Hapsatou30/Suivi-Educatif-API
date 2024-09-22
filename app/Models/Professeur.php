<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professeur extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relation avec matieres
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'prof_matieres')
        ->withPivot('id','professeur_id', 'matiere_id')
        ->withTimestamps();
    }
    public function profMatieres()
    {
        return $this->hasMany(ProfMatiere::class, 'professeur_id');
    }
    
}
