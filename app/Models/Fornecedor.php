<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'fornecedores';
    protected $primaryKey = 'idFornecedor';

    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'razaoSocial',
        'cnpjCpf',
        'inscricaoEstadual',
        'contato',
        'telefone',
        'email',
        'endereco',
        'condicaoPagamentoPadrao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('fornecedores.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('fornecedores.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}

