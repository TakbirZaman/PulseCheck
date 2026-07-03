<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'status_code',
        'response_time_ms',
        'is_success',
        'error_message',
        'pinged_at',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'pinged_at' => 'datetime',
    ];

    public function endpoint()
    {
        return $this->belongsTo(Endpoint::class);
    }
}
