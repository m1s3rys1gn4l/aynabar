<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Code extends Model
{
    protected $fillable = [
        'owner_id',
        'label',
        'kind',
        'mode',
        'barcode_format',
        'static_payload',
        'dynamic_slug',
        'dynamic_target_url',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scanEvents(): HasMany
    {
        return $this->hasMany(ScanEvent::class);
    }

    public function targetHistory(): HasMany
    {
        return $this->hasMany(DynamicTargetHistory::class);
    }
}
