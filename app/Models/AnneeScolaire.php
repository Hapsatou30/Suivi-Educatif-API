<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnneeScolaire extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec classe
    public function classes()
    {
        return $this->belongsToMany(Classe::class , 'annee_classes')
        ->withPivot('id','annee_id', 'classe_id')
        ->withTimestamps();
    }
}
