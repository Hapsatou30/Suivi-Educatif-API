<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parents extends Model
{
    use HasFactory, SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relation avec eleve (si un parent a plusieurs enfants)
    public function eleves()
    {
        return $this->hasMany(Eleve::class,'parent_id');
    }
}
