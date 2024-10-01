<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec evaluations
    public function evaluation()
    {
        return $this->belongsTo(Evaluations::class);
    }

    //relation avec bulletin
    public function bulletin()
    {
        return $this->belongsTo(Bulletin::class);
    }

    //relation avec eleves
    public function classeEleve()
    {
        return $this->belongsTo(ClasseEleve::class, 'classe_eleve_id');
    }
}
