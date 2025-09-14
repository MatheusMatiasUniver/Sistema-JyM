<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'senha',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idUsuario', 'idUsuario');
    }

    public function isAdministrador(): bool
    {
        return $this->nivelAcesso === 'Administrador';
    }

    public function isFuncionario(): bool
    {
        return $this->nivelAcesso === 'Funcionário';
    }
}