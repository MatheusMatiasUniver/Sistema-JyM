<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContaPagar extends Model
{
    use HasFactory;

    protected $table = 'contas_pagar';
    protected $primaryKey = 'idContaPagar';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'idFornecedor',
        'idFuncionario',
        'idCategoriaContaPagar',
        'documentoRef',
        'descricao',
        'valorTotal',
        'status',
        'dataVencimento',
        'dataPagamento',
        'formaPagamento',
    ];

    protected $casts = [
        'valorTotal' => 'decimal:2',
        'dataVencimento' => 'date',
        'dataPagamento' => 'date',
    ];

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'idFornecedor', 'idFornecedor');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(ContaPagarCategoria::class, 'idCategoriaContaPagar', 'idCategoriaContaPagar');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idFuncionario', 'idUsuario');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('contas_pagar.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('contas_pagar.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}

