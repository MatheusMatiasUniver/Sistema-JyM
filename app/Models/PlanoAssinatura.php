<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoAssinatura extends Model
{
    use HasFactory;

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

    public function academia()
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'idPlano', 'idPlano');
    }
}