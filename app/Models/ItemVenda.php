<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVenda extends Model
{
    use HasFactory;

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
        'precoUnitario' => 'decimal:2',
    ];

    public function vendaProduto()
    {
        return $this->belongsTo(VendaProduto::class, 'idVenda', 'idVenda');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'idProduto', 'idProduto');
    }
}