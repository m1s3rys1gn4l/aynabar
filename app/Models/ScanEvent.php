<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanEvent extends Model
{
    protected $fillable = [
        'code_id',
        'scanned_at',
        'ip_address',
        'user_agent',
        'referer',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(Code::class);
    }
}
