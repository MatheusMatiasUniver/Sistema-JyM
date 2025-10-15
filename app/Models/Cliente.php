<?php

namespace App\Models;

use App\Models\PlanoAssinatura; 
use App\Models\Mensalidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FaceDescriptor; 
use Illuminate\Support\Facades\Hash;

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
        'telefone',
        'email',
        'status',
        'foto',
        'idUsuario',
        'idPlano',
        'codigo_acesso',
    ];

    protected $casts = [
        'dataNascimento' => 'date',
    ];

    public function setAccessCodeAttribute($value)
    {
         if ($value !== null && $value !== '' && !Hash::info($value)) {
            $this->attributes['codigo_acesso'] = Hash::make($value);
        } else {
            $this->attributes['codigo_acesso'] = $value;
        }
    }

    public function setCodigoAcessoAttribute($value)
    {
        $this->attributes['codigo_acesso'] = empty($value) ? null : Hash::make($value);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function mensalidades()
    {
        return $this->hasMany(Mensalidade::class, 'idCliente', 'idCliente');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'idCliente', 'idCliente');
    }

    public function vendasProdutos()
    {
        return $this->hasMany(VendaProduto::class, 'idCliente', 'idCliente');
    }

    public function plano()
    {
        return $this->belongsTo(PlanoAssinatura::class, 'idPlano', 'idPlano');
    }

    public function getIsAtivoAttribute()
    {
        return $this->status === 'Ativo';
    }

    public function faceDescriptors()
    {
        return $this->hasMany(FaceDescriptor::class, 'cliente_id', 'idCliente');
    }

    public function getCpfFormatadoAttribute()
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->cpf);
        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }
        return $this->cpf;
    }
}