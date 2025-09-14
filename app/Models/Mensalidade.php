<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensalidade extends Model
{
    use HasFactory;

    protected $table = 'mensalidades';
    protected $primaryKey = 'idMensalidade';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataVencimento',
        'valor',
        'status',
        'dataPagamento',
    ];

    protected $casts = [
        'dataVencimento' => 'date',
        'dataPagamento' => 'date',
        'valor' => 'decimal:2',
    ];

    /**
     * Uma Mensalidade pertence a um Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function getEstaVencidaAttribute()
    {
        if ($this->status === 'Pendente' && $this->dataVencimento && $this->dataVencimento->isPast()) {
            return true;
        }
        return false;
    }

    public function getIsPagaAttribute()
    {
        return $this->status === 'Paga';
    }
}