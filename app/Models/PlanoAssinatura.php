<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoAssinatura extends Model
{
    use HasFactory;

    protected $table = 'PlanoAssinatura'; // Nome da tabela no banco de dados
    protected $primaryKey = 'idPlano'; // Chave primária da tabela
    public $timestamps = false; // Desativa timestamps

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'duracaoDias',
        'idAcademia',
    ];

    // --- Relacionamentos ---
    public function academia()
    {
        // Um PlanoAssinatura pertence a uma Academia
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }
}