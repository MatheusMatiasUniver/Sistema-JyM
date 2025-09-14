<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceDescriptor extends Model
{
    use HasFactory;

    protected $table = 'face_descriptors'; 
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'descriptor',
    ];

    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'idCliente');
    }
}