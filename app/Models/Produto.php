<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Produto extends Model
{
    protected $table = 'produtos';
    protected $primaryKey = 'idProduto';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'categoria',
        'preco',
        'estoque',
        'descricao',
        'imagem',
        'idAcademia',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function itensVenda()
    {
        return $this->hasMany(ItemVenda::class, 'idProduto', 'idProduto');
    }

    public function baixarEstoque(int $quantidade): bool
    {
        if ($this->estoque >= $quantidade) {
            $this->estoque -= $quantidade;
            return $this->save();
        }
        return false;
    }

    public function adicionarEstoque(int $quantidade): bool
    {
        $this->estoque += $quantidade;
        return $this->save();
    }

    public function temEstoque(int $quantidade): bool
    {
        return $this->estoque >= $quantidade;
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user->isFuncionario() && $user->idAcademia) {
                    $builder->where('produtos.idAcademia', $user->idAcademia);
                } elseif ($user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('produtos.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}