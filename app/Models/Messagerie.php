<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messagerie extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec utilisateur

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
