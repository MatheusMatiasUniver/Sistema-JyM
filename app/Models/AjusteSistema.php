<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AjusteSistema extends Model
{
    protected $table = 'ajustes_sistema';
    protected $primaryKey = 'idAjuste';
    public $timestamps = false;

    protected $fillable = [
        'idAcademia',
        'diaVencimentoSalarios',
    ];

    public function academia(): BelongsTo
    {
        return $this->belongsTo(Academia::class, 'idAcademia', 'idAcademia');
    }

}
