<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property bool $is_registering
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereIsRegistering($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KioskStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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