<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $idEntrada
 * @property int|null $idAcademia
 * @property int $idCliente
 * @property \Illuminate\Support\Carbon $dataHora
 * @property string $metodo
 * @property-read \App\Models\Academia|null $academia
 * @property-read \App\Models\Cliente $cliente
 * @method static Builder<static>|Entrada newModelQuery()
 * @method static Builder<static>|Entrada newQuery()
 * @method static Builder<static>|Entrada query()
 * @method static Builder<static>|Entrada whereDataHora($value)
 * @method static Builder<static>|Entrada whereIdAcademia($value)
 * @method static Builder<static>|Entrada whereIdCliente($value)
 * @method static Builder<static>|Entrada whereIdEntrada($value)
 * @method static Builder<static>|Entrada whereMetodo($value)
 * @mixin \Eloquent
 */
class Entrada extends Model
{
    protected $table = 'entradas';
    protected $primaryKey = 'idEntrada';

    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataHora',
        'metodo',
        'idAcademia',
    ];

    protected $casts = [
        'dataHora' => 'datetime',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('entradas.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('entradas.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}