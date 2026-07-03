<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'method',
        'expected_status_code',
        'timeout',
        'interval_minutes',
        'is_active',
        'last_pinged_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_pinged_at' => 'datetime',
    ];

    public function pingLogs()
    {
        return $this->hasMany(PingLog::class);
    }

    public function latestPingLog()
    {
        return $this->hasOne(PingLog::class)->latestOfMany('pinged_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
