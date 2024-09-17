<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use HasFactory,SoftDeletes;

    // Permet l'assignation en masse de tous les attributs du modèle.
    protected $guarded = [];

    //relation avec user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
