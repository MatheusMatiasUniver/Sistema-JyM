<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VendaProduto extends Model
{
    protected $table = 'venda_produtos';
    protected $primaryKey = 'idVenda';
    
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'dataVenda',
        'valorTotal',
        'formaPagamento',
        'idAcademia',
    ];

    protected $casts = [
        'dataVenda' => 'datetime',
        'valorTotal' => 'decimal:2',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function itens()
    {
        return $this->hasMany(ItemVenda::class, 'idVenda', 'idVenda');
    }

    public function itensVenda()
    {
        return $this->hasMany(ItemVenda::class, 'idVenda', 'idVenda');
    }

    public function calcularTotal(): float
    {
        return $this->itens->sum(function ($item) {
            return $item->quantidade * $item->precoUnitario;
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('venda_produtos.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('venda_produtos.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}