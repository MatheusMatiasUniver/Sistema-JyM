<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $cliente_id
 * @property array<array-key, mixed> $descriptor
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor whereClienteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor whereDescriptor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FaceDescriptor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FaceDescriptor extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'descriptor',
    ];

    protected $casts = [
        'descriptor' => 'array',
    ];

    public $timestamps = true;
}