<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    use HasFactory;

    protected $table = 'venda_produtos';
    protected $primaryKey = 'idVenda';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataVenda',
        'valorTotal',
        'tipoPagamento',
    ];

    protected $casts = [
        'dataVenda' => 'datetime',
        'valorTotal' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function itensVenda()
    {
        return $this->hasMany(ItemVenda::class, 'idVenda', 'idVenda');
    }
}