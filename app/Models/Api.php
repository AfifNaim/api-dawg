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

    /**
     * Parameter path ({order}) hasil parsing endpoint.
     */
    public function pathParams(): array
    {
        if (!preg_match_all('/\{([^}]+)\}/', (string) $this->endpoint, $matches)) {
            return [];
        }
        $seen = [];
        $out = [];
        foreach (array_unique($matches[1]) as $n) {
            $out[] = [
                'name' => $n,
                'in' => 'path',
                'required' => true,
                'example' => '',
            ];
        }
        return $out;
    }

    /**
     * Parameter query hasil parsing request_json untuk method tanpa body
     * (GET/HEAD/DELETE/OPTIONS). Untuk method ber-body, kembalikan [].
     */
    public function queryParams(): array
    {
        if (!in_array(strtoupper($this->method), ['GET', 'HEAD', 'DELETE', 'OPTIONS'], true)) {
            return [];
        }
        if (!$this->request_json) {
            return [];
        }
        $decoded = json_decode($this->request_json, true);
        if (!is_array($decoded)) {
            return [];
        }
        return array_map(fn ($k, $v) => [
            'name' => $k,
            'in' => 'query',
            'required' => false,
            'example' => $v,
        ], array_keys($decoded), array_values($decoded));
    }
}
