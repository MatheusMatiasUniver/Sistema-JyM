<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCompra extends Model
{
    protected $table = 'itens_compras';
    protected $primaryKey = 'idItemCompra';

    public $timestamps = false;

    protected $fillable = [
        'idCompra',
        'idProduto',
        'quantidade',
        'precoUnitario',
        'descontoPercent',
        'custoRateadoTotal',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'precoUnitario' => 'decimal:2',
        'descontoPercent' => 'decimal:2',
        'custoRateadoTotal' => 'decimal:2',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'idCompra', 'idCompra');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'idProduto', 'idProduto');
    }
}