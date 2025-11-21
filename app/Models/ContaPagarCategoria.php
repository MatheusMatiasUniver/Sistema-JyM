<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaPagarCategoria extends Model
{
    protected $table = 'categorias_contas_pagar';
    protected $primaryKey = 'idCategoriaContaPagar';
    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'nome',
        'ativa',
    ];
}