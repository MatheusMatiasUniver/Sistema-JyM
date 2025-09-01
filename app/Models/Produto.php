<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'Produto'; // Nome da tabela no banco de dados
    protected $primaryKey = 'idProduto'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'nome',
        'categoria',
        'preco',
        'estoque',
        'descricao',
        'imagem',
    ];

    // --- Relacionamentos ---
    public function itensVenda()
    {
        // Um Produto pode estar em muitos ItensVenda
        return $this->hasMany(ItemVenda::class, 'idProduto', 'idProduto');
    }
}