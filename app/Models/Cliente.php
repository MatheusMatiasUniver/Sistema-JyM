<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'idCliente';
    
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'cpf',
        'dataNascimento',
        'telefone',
        'email',
        'codigo_acesso',
        'status',
        'foto',
        'idUsuario',
        'idAcademia',
        'idPlano',
    ];

    protected $casts = [
        'dataNascimento' => 'date',
        'codigo_acesso' => 'string',
    ];
    
    protected $attributes = [
        'status' => 'Pendente',
    ];
    
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($cliente) {
            if (empty($cliente->codigo_acesso)) {
                $cliente->codigo_acesso = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            }
        });
    }

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(PlanoAssinatura::class, 'idPlano', 'idPlano');
    }

    public function mensalidades()
    {
        return $this->hasMany(Mensalidade::class, 'idCliente', 'idCliente');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'idCliente', 'idCliente');
    }

    public function vendas()
    {
        return $this->hasMany(VendaProduto::class, 'idCliente', 'idCliente');
    }

    public function faceDescriptors()
    {
        return $this->hasMany(FaceDescriptor::class, 'idCliente', 'idCliente');
    }

    public function isAtivo(): bool
    {
        return $this->status === 'Ativo';
    }

    public function isSuspenso(): bool
    {
        return $this->status === 'Suspenso';
    }

    public function isInadimplente(): bool
    {
        return $this->status === 'Inadimplente';
    }

    public function isInativo(): bool
    {
        return $this->status === 'Inativo';
    }

    public function isPendente(): bool
    {
        return $this->status === 'Pendente';
    }

    public function podeAcessarAcademia(): bool
    {
        return in_array($this->status, ['Ativo', 'Inadimplente']);
    }

    public function podeDeletar(): bool
    {
        return $this->mensalidades()->count() === 0 && 
               $this->entradas()->count() === 0 && 
               $this->vendas()->count() === 0;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Ativo' => 'green',
            'Suspenso' => 'yellow',
            'Inadimplente' => 'orange',
            'Inativo' => 'red',
            'Pendente' => 'blue',
            default => 'gray'
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'Ativo' => 'bg-green-100 text-green-800',
            'Suspenso' => 'bg-yellow-100 text-yellow-800',
            'Inadimplente' => 'bg-orange-100 text-orange-800',
            'Inativo' => 'bg-red-100 text-red-800',
            'Pendente' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    protected static function booted()
    {
        static::addGlobalScope('academia', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user && $user->nivelAcesso === 'Funcionário') {
                    $academia = DB::table('usuario_academia')
                        ->where('idUsuario', $user->idUsuario)
                        ->join('academias', 'usuario_academia.idAcademia', '=', 'academias.idAcademia')
                        ->first();
                    if ($academia) {
                        $builder->where('clientes.idAcademia', $academia->idAcademia);
                    }
                } elseif ($user && $user->nivelAcesso === 'Administrador') {
                    $academiaId = session('academia_selecionada');
                    if ($academiaId) {
                        $builder->where('clientes.idAcademia', $academiaId);
                    }
                }
            }
        });
    }
}