<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVenda extends Model
{
    use HasFactory;

    // A tabela no BD é 'ItensVenda' (CamelCase, singular), o que não segue o padrão plural snake_case do Laravel (itens_vendas)
    protected $table = 'ItensVenda';
    protected $primaryKey = 'idItem';
    public $timestamps = false;

    protected $fillable = [
        'idVenda',
        'idProduto',
        'quantidade',
        'precoUnitario',
    ];

    // --- Relacionamentos ---
    public function venda()
    {
        // Um ItemVenda pertence a uma VendaProduto
        return $this->belongsTo(VendaProduto::class, 'idVenda', 'idVenda');
    }

    public function produto()
    {
        // Um ItemVenda pertence a um Produto
        return $this->belongsTo(Produto::class, 'idProduto', 'idProduto');
    }
}