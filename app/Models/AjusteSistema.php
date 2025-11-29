<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AjusteSistema extends Model
{
    public const FORMAS_PAGAMENTO_PADRAO = [
        'Dinheiro',
        'Cartão de Crédito',
        'Cartão de Débito',
        'PIX',
        'Boleto',
    ];

    protected $table = 'ajustes_sistema';
    protected $primaryKey = 'idAjuste';
    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'diaVencimentoSalarios',
        'clienteOpcionalVenda',
        'formasPagamentoAceitas',
        'permitirEdicaoManualEstoque',
    ];

    protected $casts = [
        'clienteOpcionalVenda' => 'boolean',
        'formasPagamentoAceitas' => 'array',
        'permitirEdicaoManualEstoque' => 'boolean',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function getFormasPagamentoAtivasAttribute(): array
    {
        $configuradas = is_array($this->formasPagamentoAceitas) ? $this->formasPagamentoAceitas : [];
        $validas = array_values(array_intersect(self::FORMAS_PAGAMENTO_PADRAO, $configuradas));

        return $validas ?: self::FORMAS_PAGAMENTO_PADRAO;
    }

    public static function obterOuCriarParaAcademia(int $academiaId): self
    {
        return self::firstOrCreate(
            ['idAcademia' => $academiaId],
            [
                'diaVencimentoSalarios' => 5,
                'clienteOpcionalVenda' => false,
                'formasPagamentoAceitas' => self::FORMAS_PAGAMENTO_PADRAO,
                'permitirEdicaoManualEstoque' => false,
            ]
        );
    }

    public static function formasPagamentoParaAcademia(?int $academiaId): array
    {
        if (!$academiaId) {
            return self::FORMAS_PAGAMENTO_PADRAO;
        }

        return self::obterOuCriarParaAcademia((int) $academiaId)->formasPagamentoAtivas;
    }

}
