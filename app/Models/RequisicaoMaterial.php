<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequisicaoMaterial extends Model
{
    protected $table = 'requisicoes_materiais';
    protected $primaryKey = 'idRequisicao';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'idMaterial',
        'quantidade',
        'centroCusto',
        'data',
        'usuarioId',
        'motivo',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'idMaterial', 'idMaterial');
    }
}

