<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idUsuario';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'usuario',
        'senha',
        'nivelAcesso',
        'idAcademia',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'senha',
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
            'senha' => 'hashed',
        ];
    }

    /**
     * Get the password for the user (Laravel Auth compatibility).
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    /**
     * Relacionamento com academias (Many-to-Many)
     */
    public function academias(): BelongsToMany
    {
        return $this->belongsToMany(
            Academia::class,
            'usuario_academia',
            'idUsuario',
            'idAcademia'
        );
    }

    public function isAdministrador(): bool
    {
        return $this->nivelAcesso === 'Administrador';
    }

    public function isFuncionario(): bool
    {
        return $this->nivelAcesso === 'Funcionário';
    }

    public function temAcessoAcademia($academiaId): bool
    {
        return $this->academias()->where('academias.idAcademia', $academiaId)->exists();
    }

    /**
     * Relacionamento com clientes
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idUsuario', 'idUsuario');
    }

    public function podeDeletar(): bool
    {
        return $this->clientes()->count() === 0;
    }
}