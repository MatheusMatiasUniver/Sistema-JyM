<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManutencaoEquipamento extends Model
{
    protected $table = 'manutencoes_equipamento';
    protected $primaryKey = 'idManutencao';

    public $timestamps = false;

    protected $fillable = [
        'idEquipamento',
        'tipo',
        'dataProgramada',
        'dataExecucao',
        'custo',
        'fornecedorId',
        'observacoes',
    ];

    protected $casts = [
        'custo' => 'decimal:2',
        'dataProgramada' => 'date',
        'dataExecucao' => 'date',
    ];

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class, 'idEquipamento', 'idEquipamento');
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedorId', 'idFornecedor');
    }
}

