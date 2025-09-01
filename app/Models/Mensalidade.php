<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensalidade extends Model
{
    use HasFactory;

    protected $table = 'mensalidade';
    protected $primaryKey = 'idMensalidade';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataVencimento',
        'valor',
        'status',
        'dataPagamento',
    ];

    // --- Relacionamentos ---
    public function cliente()
    {
        // Uma Mensalidade pertence a um Cliente
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }
}