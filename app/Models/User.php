<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'idUsuario';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'usuario',
        'email',
        'senha',
        'nivelAcesso',
    ];

    protected $hidden = [
        'senha'
    ];

    protected $casts = [
        // 'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idUsuario', 'idUsuario');
    }
}