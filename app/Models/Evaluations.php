<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluations extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec ClasseProf
    public function classeProf()
    {
        return $this->belongsTo(ClasseProf::class);
    }

    //relation avec note
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
