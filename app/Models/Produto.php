<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $idProduto
 * @property int|null $idAcademia
 * @property string|null $nome
 * @property int|null $idCategoria
 * @property numeric|null $preco
 * @property int|null $estoque
 * @property string|null $descricao
 * @property string|null $imagem
 * @property-read \App\Models\Academia|null $academia
 * @property-read \App\Models\Categoria|null $categoria
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemVenda> $itensVenda
 * @property-read int|null $itens_venda_count
 * @method static Builder<static>|Produto newModelQuery()
 * @method static Builder<static>|Produto newQuery()
 * @method static Builder<static>|Produto query()
 * @method static Builder<static>|Produto whereDescricao($value)
 * @method static Builder<static>|Produto whereEstoque($value)
 * @method static Builder<static>|Produto whereIdAcademia($value)
 * @method static Builder<static>|Produto whereIdCategoria($value)
 * @method static Builder<static>|Produto whereIdProduto($value)
 * @method static Builder<static>|Produto whereImagem($value)
 * @method static Builder<static>|Produto whereNome($value)
 * @method static Builder<static>|Produto wherePreco($value)
 * @mixin \Eloquent
 */
class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    protected $primaryKey = 'idProduto';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'idCategoria',
        'idMarca',
        'idFornecedor',
        'preco',
        'estoque',
        'descricao',
        'imagem',
        'idAcademia',
        'precoCompra',
        'custoMedio',
        'estoqueMinimo',
        'unidadeMedida',
        'codigoBarras',
        'vendavel',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'precoCompra' => 'decimal:2',
        'custoMedio' => 'decimal:2',
        'estoqueMinimo' => 'integer',
        'vendavel' => 'boolean',
    ];

    public function atingiuEstoqueMinimo(): bool
    {
        return $this->estoqueMinimo !== null && $this->estoque <= $this->estoqueMinimo;
    }

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'idMarca', 'idMarca');
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'idFornecedor', 'idFornecedor');
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

    public function podeDeletar(): bool
    {
        return $this->itensVenda()->count() === 0;
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('produtos.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('produtos.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}
