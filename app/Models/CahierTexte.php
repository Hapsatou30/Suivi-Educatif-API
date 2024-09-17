<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CahierTexte extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec prof_matiere
    public function profMatiere()
    {
        return $this->belongsTo(ProfMatiere::class);
    }
}
