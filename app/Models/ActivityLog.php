<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'usuarioId',
        'modulo',
        'acao',
        'entidade',
        'entidadeId',
        'dados',
    ];

    protected $casts = [
        'dados' => 'array',
    ];
}