<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academia extends Model
{
    use HasFactory;

    protected $table = 'Academia'; // Nome da tabela no banco de dados
    protected $primaryKey = 'idAcademia'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'nome',
        'CNPJ',
        'telefone',
        'email',
        'endereco',
        'responsavel',
    ];

    // --- Relacionamentos ---
    public function planosAssinatura()
    {
        // Uma Academia tem muitos Planos de Assinatura
        return $this->hasMany(PlanoAssinatura::class, 'idAcademia', 'idAcademia');
    }
}