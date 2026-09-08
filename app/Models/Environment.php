<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Environment extends Model
{
    protected $fillable = [
        'project_id', 'name', 'base_url', 'token', 'proxy_target', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
