<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $idCategoria
 * @property string $nome
 * @property string|null $descricao
 * @property string $status
 * @property int $idAcademia
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Academia $academia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Produto> $produtos
 * @property-read int|null $produtos_count
 * @method static Builder<static>|Categoria ativas()
 * @method static Builder<static>|Categoria newModelQuery()
 * @method static Builder<static>|Categoria newQuery()
 * @method static Builder<static>|Categoria porAcademia(int $academiaId)
 * @method static Builder<static>|Categoria query()
 * @method static Builder<static>|Categoria whereCreatedAt($value)
 * @method static Builder<static>|Categoria whereDescricao($value)
 * @method static Builder<static>|Categoria whereIdAcademia($value)
 * @method static Builder<static>|Categoria whereIdCategoria($value)
 * @method static Builder<static>|Categoria whereNome($value)
 * @method static Builder<static>|Categoria whereStatus($value)
 * @method static Builder<static>|Categoria whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'idCategoria';

    protected $fillable = [
        'nome',
        'descricao',
        'status',
        'idAcademia',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class, 'idCategoria', 'idCategoria');
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'Ativo');
    }

    public function scopePorAcademia(Builder $query, int $academiaId): Builder
    {
        return $query->where('idAcademia', $academiaId);
    }

    public function isAtiva(): bool
    {
        return $this->status === 'Ativo';
    }

    public function contarProdutos(): int
    {
        return $this->produtos()->count();
    }

    public function podeDeletar(): bool
    {
        return $this->contarProdutos() === 0;
    }
}
