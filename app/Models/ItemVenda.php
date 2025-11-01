<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $idItem
 * @property int|null $idVenda
 * @property int|null $idProduto
 * @property int|null $quantidade
 * @property numeric|null $precoUnitario
 * @property-read float $subtotal
 * @property-read \App\Models\Produto|null $produto
 * @property-read \App\Models\VendaProduto|null $venda
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda whereIdItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda whereIdProduto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda whereIdVenda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda wherePrecoUnitario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVenda whereQuantidade($value)
 * @mixin \Eloquent
 */
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