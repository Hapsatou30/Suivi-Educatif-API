<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relation avec l'admin
    public function admin() 
    {
        return $this->hasOne(Admin::class);
    }

    //relation avec le professeur
    public function professeur()
    {
        return $this->hasOne(Professeur::class);
    }

    //relation avec eleve
    public function eleve()
    {
        return $this->hasOne(Eleve::class);
    }

    //relation avec parent
    public function parent()
    {
        return $this->hasOne(Parents::class);
    }

    //relation avec messagerie
    public function messageries()
    {
        return $this->hasMany(Messagerie::class);
    }

    //relation avec notif
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
