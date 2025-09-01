<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    use HasFactory;

    protected $table = 'VendaProduto'; // Nome da tabela no banco de dados
    protected $primaryKey = 'idVenda'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'idCliente',
        'dataVenda',
        'valorTotal',
        'tipoPagamento',
    ];

    // --- Relacionamentos ---
    public function cliente()
    {
        // Uma VendaProduto pertence a um Cliente
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function itensVenda()
    {
        // Uma VendaProduto tem muitos ItensVenda
        return $this->hasMany(ItemVenda::class, 'idVenda', 'idVenda');
    }
}