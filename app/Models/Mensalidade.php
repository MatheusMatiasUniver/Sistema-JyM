<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int $idMensalidade
 * @property int $idCliente
 * @property int $idPlano
 * @property int $idAcademia Identificador da academia
 * @property \Illuminate\Support\Carbon $dataVencimento
 * @property numeric $valor
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $dataPagamento
 * @property string|null $formaPagamento Forma de pagamento utilizada
 * @property-read \App\Models\Cliente $cliente
 * @property-read \App\Models\PlanoAssinatura $plano
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereDataPagamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereDataVencimento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereFormaPagamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereIdAcademia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereIdCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereIdMensalidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereIdPlano($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mensalidade whereValor($value)
 * @mixin \Eloquent
 */
class Mensalidade extends Model
{
    protected $table = 'mensalidades';
    protected $primaryKey = 'idMensalidade';

    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'idPlano',
        'idAcademia',
        'dataVencimento',
        'dataPagamento',
        'valor',
        'status',
        'formaPagamento',
    ];

    protected $casts = [
        'dataVencimento' => 'date',
        'dataPagamento' => 'date',
        'valor' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente')->withTrashed();
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(PlanoAssinatura::class, 'idPlano', 'idPlano');
    }

    public function estaVencida(): bool
    {
        return $this->status !== 'Paga' && $this->dataVencimento < Carbon::today();
    }

    public function estaPaga(): bool
    {
        return $this->status === 'Paga';
    }

    public function diasAteVencimento(): int
    {
        return Carbon::today()->diffInDays($this->dataVencimento, false);
    }
}