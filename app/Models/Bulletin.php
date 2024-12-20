<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bulletin extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec note
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    //relation avec eleves
    public function classeEleve()
    {
        return $this->belongsTo(ClasseEleve::class, 'classe_eleve_id');
    }
}
