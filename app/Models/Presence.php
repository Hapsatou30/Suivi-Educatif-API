<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec classe_eleve
    public function classeEleve()
    {
        return $this->belongsTo(ClasseEleve::class,'classe_eleve_id');
    }

    //relation avec classe_prof
    public function classeProf()
    {
        return $this->belongsTo(ClasseProf::class,'classe_prof_id');
    }
}
