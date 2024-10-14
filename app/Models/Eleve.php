<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eleve extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relation avec parents (si un enfant est lié à un seul parent)
    public function parent()
    {
        return $this->belongsTo(Parents::class,'parent_id');
    }

    //relation avec annee_classes (les classes où l'élève est inscrit)
    public function anneeClasses()
    {
        return $this->belongsToMany(AnneeClasse::class, 'classe_eleves')
        ->withPivot('id', 'annee_classe_id', 'eleve_id')
        ->withTimestamps();
    }
    
public function classeEleves()
{
    return $this->hasMany(ClasseEleve::class);
}

    
}
