<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Api extends Model
{
    protected $fillable = [
        'project_id', 'group_id', 'name', 'description', 'method',
        'endpoint', 'headers', 'request_json', 'example_request', 'success_response',
        'error_response', 'sort_order',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
