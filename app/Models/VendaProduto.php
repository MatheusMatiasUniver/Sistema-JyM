<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $idVenda
 * @property int|null $idAcademia
 * @property int|null $idCliente
 * @property int|null $idUsuario
 * @property \Illuminate\Support\Carbon|null $dataVenda
 * @property numeric|null $valorTotal
 * @property string|null $formaPagamento
 * @property-read \App\Models\Academia|null $academia
 * @property-read \App\Models\Cliente|null $cliente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemVenda> $itens
 * @property-read int|null $itens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemVenda> $itensVenda
 * @property-read int|null $itens_venda_count
 * @method static Builder<static>|VendaProduto newModelQuery()
 * @method static Builder<static>|VendaProduto newQuery()
 * @method static Builder<static>|VendaProduto query()
 * @method static Builder<static>|VendaProduto whereDataVenda($value)
 * @method static Builder<static>|VendaProduto whereFormaPagamento($value)
 * @method static Builder<static>|VendaProduto whereIdAcademia($value)
 * @method static Builder<static>|VendaProduto whereIdCliente($value)
 * @method static Builder<static>|VendaProduto whereIdUsuario($value)
 * @method static Builder<static>|VendaProduto whereIdVenda($value)
 * @method static Builder<static>|VendaProduto whereValorTotal($value)
 * @mixin \Eloquent
 */
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