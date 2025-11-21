<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Marca extends Model
{
    protected $table = 'marcas';
    protected $primaryKey = 'idMarca';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'nome',
        'paisOrigem',
        'site',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class, 'idMarca', 'idMarca');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('marcas.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('marcas.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}