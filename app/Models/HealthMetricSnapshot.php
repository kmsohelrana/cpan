<?php

namespace App\Models;

use App\Enums\HealthStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthMetricSnapshot extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'value' => 'float',
        'payload' => 'array',
        'recorded_at' => 'datetime',
        'status' => HealthStatusEnum::class,
    ];

    public function getStatusBadgeAttribute(): string
    {
        return $this->status?->badge() ?? 'N/A';
    }
}
