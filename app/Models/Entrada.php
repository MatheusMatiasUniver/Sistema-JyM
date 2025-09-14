<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $primaryKey = 'idEntrada';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataHora',
        'metodo',
    ];

    protected $casts = [
        'dataHora' => 'datetime',
    ];
 
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }
}