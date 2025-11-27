<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method bool isAdministrador()
 * @method bool isFuncionario()
 * @method bool temAcessoAcademia(int $academiaId)
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany academias()
 * @method bool podeDeletar()
 * @property int $idUsuario
 * @property string $nome
 * @property string $usuario
 * @property string|null $email
 * @property string $senha
 * @property string $nivelAcesso
 * @property int|null $idAcademia ID da academia à qual o funcionário está vinculado.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Academia> $academias
 * @property-read int|null $academias_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cliente> $clientes
 * @property-read int|null $clientes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdAcademia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNivelAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsuario($value)
 * @mixin \Eloquent
 */
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
        'salarioMensal',
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