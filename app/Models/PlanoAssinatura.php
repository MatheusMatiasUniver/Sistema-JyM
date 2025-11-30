<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $idPlano
 * @property string|null $nome
 * @property string|null $descricao
 * @property numeric|null $valor
 * @property int|null $duracaoDias
 * @property int|null $idAcademia
 * @property-read \App\Models\Academia|null $academia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cliente> $clientes
 * @property-read int|null $clientes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mensalidade> $mensalidades
 * @property-read int|null $mensalidades_count
 * @method static Builder<static>|PlanoAssinatura newModelQuery()
 * @method static Builder<static>|PlanoAssinatura newQuery()
 * @method static Builder<static>|PlanoAssinatura query()
 * @method static Builder<static>|PlanoAssinatura whereDescricao($value)
 * @method static Builder<static>|PlanoAssinatura whereDuracaoDias($value)
 * @method static Builder<static>|PlanoAssinatura whereIdAcademia($value)
 * @method static Builder<static>|PlanoAssinatura whereIdPlano($value)
 * @method static Builder<static>|PlanoAssinatura whereNome($value)
 * @method static Builder<static>|PlanoAssinatura whereValor($value)
 * @mixin \Eloquent
 */
class PlanoAssinatura extends Model
{
    use HasFactory;

    protected $table = 'plano_assinaturas';
    protected $primaryKey = 'idPlano';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'duracaoDias',
        'idAcademia',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'duracaoDias' => 'integer',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function mensalidades()
    {
        return $this->hasMany(Mensalidade::class, 'idPlano', 'idPlano');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idPlano', 'idPlano');
    }

    public function podeDeletar(): bool
    {
        return $this->clientes()->count() === 0 && $this->mensalidades()->count() === 0;
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('plano_assinaturas.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('plano_assinaturas.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}