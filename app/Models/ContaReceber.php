<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContaReceber extends Model
{
    use HasFactory;

    protected $table = 'contas_receber';
    protected $primaryKey = 'idContaReceber';
    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'idCliente',
        'documentoRef',
        'descricao',
        'valorTotal',
        'status',
        'dataVencimento',
        'dataRecebimento',
        'formaRecebimento',
    ];

    protected $casts = [
        'valorTotal' => 'decimal:2',
        'dataVencimento' => 'date',
        'dataRecebimento' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isFuncionario() && isset($user->idAcademia)) {
                    $builder->where('contas_receber.idAcademia', $user->idAcademia);
                } elseif ($user && $user->isAdministrador()) {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('contas_receber.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}