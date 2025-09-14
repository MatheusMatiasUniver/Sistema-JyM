<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FaceDescriptor; 

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'idCliente';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'cpf',
        'dataNascimento',
        'status',
        'foto',
        'idUsuario',
        'idPlano',
    ];

    protected $casts = [
        'dataNascimento' => 'date',
    ];

    /**
     * Um Cliente pertence a um Usuário (o usuário que o cadastrou).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    /**
     * Um Cliente tem muitas Mensalidades.
     */
    public function mensalidades()
    {
        return $this->hasMany(Mensalidade::class, 'idCliente', 'idCliente');
    }

    /**
     * Um Cliente tem muitas Entradas.
     */
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'idCliente', 'idCliente');
    }

    /**
     * Um Cliente pode ter muitas Vendas de Produtos.
     */
    public function vendasProdutos()
    {
        return $this->hasMany(VendaProduto::class, 'idCliente', 'idCliente');
    }

    /**
     * Um Cliente pertence a um Plano de Assinatura.
     */
    public function plano()
    {
        return $this->belongsTo(PlanoAssinatura::class, 'idPlano', 'idPlano');
    }

    /**
     * Acessor para verificar se o cliente está ativo.
     */
    public function getIsAtivoAttribute()
    {
        return $this->status === 'Ativo';
    }

    public function faceDescriptors()
    {
        return $this->hasMany(FaceDescriptor::class, 'cliente_id', 'idCliente');
    }

    /**
     * Acessor para obter o CPF formatado.
     */
    public function getCpfFormatadoAttribute()
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->cpf);
        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }
        return $this->cpf;
    }
}