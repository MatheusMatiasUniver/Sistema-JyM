<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academia extends Model
{
    use HasFactory;

    protected $table = 'academias';
    protected $primaryKey = 'idAcademia';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'CNPJ',
        'telefone',
        'email',
        'endereco',
        'responsavel',
    ];
    
    public function planosAssinatura()
    {
        return $this->hasMany(PlanoAssinatura::class, 'idAcademia', 'idAcademia');
    }
}