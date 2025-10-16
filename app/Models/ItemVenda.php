<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVenda extends Model
{
    protected $table = 'itens_vendas';
    protected $primaryKey = 'idItem';

    public $timestamps = false;

    protected $fillable = [
        'idVenda',
        'idProduto',
        'quantidade',
        'precoUnitario',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'precoUnitario' => 'decimal:2',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(VendaProduto::class, 'idVenda', 'idVenda');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'idProduto', 'idProduto');
    }

    public function calcularSubtotal(): float
    {
        return $this->quantidade * $this->precoUnitario;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->calcularSubtotal();
    }
}