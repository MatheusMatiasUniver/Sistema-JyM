<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

enum StatusEquipamento: string
{
    case ATIVO = 'Ativo';
    case EM_MANUTENCAO = 'Em Manutenção';
    case DESATIVADO = 'Desativado';
}

class Equipamento extends Model
{
    protected $table = 'equipamentos';
    protected $primaryKey = 'idEquipamento';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'descricao',
        'fabricante',
        'modelo',
        'numeroSerie',
        'dataAquisicao',
        'valorAquisicao',
        'garantiaFim',
        'centroCusto',
        'status',
    ];

    protected $casts = [
        'valorAquisicao' => 'decimal:2',
        'dataAquisicao' => 'date',
        'garantiaFim' => 'date',
        'status' => StatusEquipamento::class,
    ];

    public function manutencoes(): HasMany
    {
        return $this->hasMany(ManutencaoEquipamento::class, 'idEquipamento', 'idEquipamento');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('equipamentos.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('equipamentos.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}

