<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $idAcademia
 * @property string|null $nome
 * @property string|null $CNPJ
 * @property string|null $telefone
 * @property string|null $email
 * @property string|null $endereco
 * @property string|null $responsavel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $administradores
 * @property-read int|null $administradores_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cliente> $clientes
 * @property-read int|null $clientes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entrada> $entradas
 * @property-read int|null $entradas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $funcionarios
 * @property-read int|null $funcionarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlanoAssinatura> $planos
 * @property-read int|null $planos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Produto> $produtos
 * @property-read int|null $produtos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendaProduto> $vendas
 * @property-read int|null $vendas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereCNPJ($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereEndereco($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereIdAcademia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereResponsavel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Academia whereTelefone($value)
 * @mixin \Eloquent
 */
class Academia extends Model
{
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

    public function podeDeletar(): bool
    {
        return $this->clientes()->count() === 0 && 
               $this->produtos()->count() === 0 && 
               $this->vendas()->count() === 0 && 
               $this->entradas()->count() === 0 && 
               $this->planos()->count() === 0 && 
               $this->funcionarios()->count() === 0;
    }
}