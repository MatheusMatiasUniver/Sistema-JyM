<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $primaryKey = 'idCompra';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'idFornecedor',
        'dataEmissao',
        'status',
        'valorProdutos',
        'valorFrete',
        'valorDesconto',
        'valorImpostos',
        'valorTotal',
        'observacoes',
    ];

    protected $casts = [
        'valorProdutos' => 'decimal:2',
        'valorFrete' => 'decimal:2',
        'valorDesconto' => 'decimal:2',
        'valorImpostos' => 'decimal:2',
        'valorTotal' => 'decimal:2',
        'dataEmissao' => 'datetime',
    ];

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'idFornecedor', 'idFornecedor');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemCompra::class, 'idCompra', 'idCompra');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('compras.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('compras.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}