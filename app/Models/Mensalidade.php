<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Mensalidade extends Model
{
    protected $table = 'mensalidades';
    protected $primaryKey = 'idMensalidade';

    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'idPlano',
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
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(PlanoAssinatura::class, 'idPlano', 'idPlano');
    }

    public function estaVencida(): bool
    {
        return $this->status !== 'Pago' && $this->dataVencimento < Carbon::today();
    }

    public function estaPaga(): bool
    {
        return $this->status === 'Pago';
    }

    public function diasAteVencimento(): int
    {
        return Carbon::today()->diffInDays($this->dataVencimento, false);
    }
}