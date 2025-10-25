<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PlanoAssinatura extends Model
{
    protected $table = 'plano_assinaturas';
    protected $primaryKey = 'idPlano';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'duracaoDias',
        'idAcademia',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'duracaoDias' => 'integer',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function mensalidades()
    {
        return $this->hasMany(Mensalidade::class, 'idPlano', 'idPlano');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idPlano', 'idPlano');
    }

    public function podeDeletar(): bool
    {
        return $this->clientes()->count() === 0 && $this->mensalidades()->count() === 0;
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('plano_assinaturas.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('plano_assinaturas.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}