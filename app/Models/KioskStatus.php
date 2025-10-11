<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KioskStatus extends Model
{
    use HasFactory;

    protected $table = 'kiosk_status';

    protected $fillable = [
        'is_registering',
        'message',
        'expires_at',
    ];

    protected $casts = [
        'is_registering' => 'boolean',
        'expires_at' => 'datetime',
    ];
}