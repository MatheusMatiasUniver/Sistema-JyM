<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

enum StatusManutencao: string
{
    case PENDENTE = 'Pendente';
    case CONCLUIDA = 'Concluída';
}

class ManutencaoEquipamento extends Model
{
    protected $table = 'manutencoes_equipamento';
    protected $primaryKey = 'idManutencao';

    public $timestamps = false;

    protected $fillable = [
        'idEquipamento',
        'tipo',
        'dataSolicitacao',
        'dataProgramada',
        'dataExecucao',
        'descricao',
        'servicoRealizado',
        'custo',
        'fornecedorId',
        'responsavel',
        'status',
    ];

    protected $casts = [
        'dataSolicitacao' => 'date',
        'dataProgramada' => 'date',
        'dataExecucao' => 'date',
        'custo' => 'decimal:2',
        'status' => StatusManutencao::class,
    ];

    /**
     * Relacionamento com Equipamento (sem global scope para evitar problemas de contexto)
     */
    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class, 'idEquipamento', 'idEquipamento')->withoutGlobalScopes();
    }

    /**
     * Relacionamento com Fornecedor
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedorId', 'idFornecedor');
    }

    /**
     * Verifica se a manutenção está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === StatusManutencao::PENDENTE;
    }

    /**
     * Verifica se a manutenção está concluída
     */
    public function isConcluida(): bool
    {
        return $this->status === StatusManutencao::CONCLUIDA;
    }

    /**
     * Finaliza a manutenção e atualiza o status do equipamento para "Ativo"
     */
    public function finalizarManutencao(array $dados): bool
    {
        $this->dataExecucao = $dados['dataExecucao'] ?? now();
        $this->servicoRealizado = $dados['servicoRealizado'] ?? null;
        $this->custo = $dados['custo'] ?? null;
        $this->responsavel = $dados['responsavel'] ?? null;
        $this->status = StatusManutencao::CONCLUIDA;

        if ($this->save()) {
            // Atualizar status do equipamento para "Ativo"
            if ($this->equipamento) {
                $this->equipamento->update(['status' => StatusEquipamento::ATIVO->value]);
            }
            return true;
        }

        return false;
    }
}
