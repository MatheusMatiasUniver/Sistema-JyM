<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'Entrada'; // Nome da tabela no banco de dados
    protected $primaryKey = 'idEntrada'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'idCliente',
        'dataHora',
        'metodo',
    ];

    // --- Relacionamentos ---
    public function cliente()
    {
        // Uma Entrada pertence a um Cliente
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }
}