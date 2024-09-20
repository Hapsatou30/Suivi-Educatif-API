<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horaire extends Model
{
    use HasFactory;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec classe_prof
    public function classeProf()
    {
        return $this->belongsTo(ClasseProf::class);
    }
}
