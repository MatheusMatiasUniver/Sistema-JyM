<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academia extends Model
{
    protected $table = 'academias';
    protected $primaryKey = 'idAcademia';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
        'endereco',
        'responsavel',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idAcademia', 'idAcademia');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'idAcademia', 'idAcademia');
    }

    public function vendas()
    {
        return $this->hasMany(VendaProduto::class, 'idAcademia', 'idAcademia');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'idAcademia', 'idAcademia');
    }

    public function planos()
    {
        return $this->hasMany(PlanoAssinatura::class, 'idAcademia', 'idAcademia');
    }

    public function funcionarios()
    {
        return $this->hasMany(User::class, 'idAcademia', 'idAcademia')
                    ->where('nivelAcesso', 'Funcionário');
    }

    public function administradores()
    {
        return $this->belongsToMany(User::class, 'usuario_academia', 'idAcademia', 'idUsuario')
                    ->where('nivelAcesso', 'Administrador')
                    ->withTimestamps();
    }
}