<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
