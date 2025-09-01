<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    
    protected $primaryKey = 'idCliente'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'nome',
        'cpf',
        'dataNascimento',
        'status',
        'foto',
        'idUsuario', // A chave estrangeira também deve ser fillable se for atribuída diretamente
    ];

    // --- Relacionamentos ---
    public function usuario()
    {
        // Um Cliente pertence a um Usuário (o usuário que o cadastrou)
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function mensalidades()
    {
        // Um Cliente tem muitas Mensalidades
        return $this->hasMany(Mensalidade::class, 'idCliente', 'idCliente');
    }

    public function entradas()
    {
        // Um Cliente tem muitas Entradas
        return $this->hasMany(Entrada::class, 'idCliente', 'idCliente');
    }

    public function vendas()
    {
        // Um Cliente pode ter muitas Vendas (VendaProduto)
        return $this->hasMany(VendaProduto::class, 'idCliente', 'idCliente');
    }
}