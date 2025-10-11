<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $primaryKey = 'idProduto';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'categoria',
        'preco',
        'estoque',
        'descricao',
        'imagem',
    ];

    public function itensVenda()
    {
        return $this->hasMany(ItemVenda::class, 'idProduto', 'idProduto');
    }
}