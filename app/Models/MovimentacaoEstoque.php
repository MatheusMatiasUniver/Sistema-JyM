<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MovimentacaoEstoque extends Model
{
    protected $table = 'movimentacoes_estoque';
    protected $primaryKey = 'idMovimentacao';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'idProduto',
        'tipo',
        'quantidade',
        'custoUnitario',
        'custoTotal',
        'origem',
        'referenciaId',
        'motivo',
        'dataMovimentacao',
        'usuarioId',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'custoUnitario' => 'decimal:2',
        'custoTotal' => 'decimal:2',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'idProduto', 'idProduto');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('movimentacoes_estoque.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('movimentacoes_estoque.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}

