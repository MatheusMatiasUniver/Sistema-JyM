<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiais';
    protected $primaryKey = 'idMaterial';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'descricao',
        'estoque',
        'unidadeMedida',
        'estoqueMinimo',
    ];

    protected $casts = [
        'estoque' => 'integer',
        'estoqueMinimo' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('materiais.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('materiais.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}

