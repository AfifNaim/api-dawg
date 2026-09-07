<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class OpenApiController extends Controller
{
    public function spec(Project $project): JsonResponse
    {
        $project->load(['groups.apis', 'apis']);

        $paths = [];
        $tags = [];
        $hasToken = filled($project->token);

        foreach ($project->groups as $group) {
            $tags[] = ['name' => $group->name];
            foreach ($group->apis as $api) {
                $this->addPath($paths, $api, $group->name, $hasToken);
            }
        }

        foreach ($project->apis->whereNull('group_id') as $api) {
            $this->addPath($paths, $api, null, $hasToken);
        }

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => $project->name,
                'description' => $project->description ?? '',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => rtrim($project->base_url, '/')],
            ],
            'tags' => $tags,
            'paths' => $paths,
        ];

        if ($hasToken) {
            $spec['components'] = [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ];
        }

        return response()->json($spec);
    }

    private function addPath(array &$paths, Api $api, ?string $tag = null, bool $hasToken = false): void
    {
        $path = '/' . ltrim($api->endpoint, '/');
        $operation = [
            'tags' => $tag ? [$tag] : [],
            'summary' => $api->name,
            'description' => $api->description ?? '',
            'operationId' => str($api->name)->slug('_'),
            'responses' => $this->responses($api),
        ];

        if ($hasToken) {
            $operation['security'] = [['bearerAuth' => []]];
        }

        $parameters = array_merge($this->pathParams($api), $this->headers($api));

        if ($parameters) {
            $operation['parameters'] = $parameters;
        }

        if ($api->request_json) {
            $operation['requestBody'] = [
                'required' => false,
                'content' => [
                    'application/json' => [
                        'schema' => $this->schema($api->request_json),
                        'example' => json_decode($api->request_json, true),
                    ],
                ],
            ];
        }

        $paths[$path][strtolower($api->method)] = $operation;
    }

    private function headers(Api $api): array
    {
        if (!$api->headers) {
            return [];
        }
        $decoded = json_decode($api->headers, true);
        if (!is_array($decoded)) {
            return [];
        }
        $params = [];
        foreach ($decoded as $name => $example) {
            $params[] = [
                'name' => $name,
                'in' => 'header',
                'required' => true,
                'schema' => ['type' => 'string'],
                'example' => $example,
            ];
        }
        return $params;
    }

    /**
     * Ekstrak placeholder {param} pada endpoint sebagai path parameter,
     * supaya Swagger UI menampilkan field input & menggantinya dengan nilai asli.
     */
    private function pathParams(Api $api): array
    {
        if (!preg_match_all('/\{([^}]+)\}/', (string) $api->endpoint, $matches)) {
            return [];
        }

        $params = [];
        foreach (array_unique($matches[1]) as $name) {
            $params[] = [
                'name' => $name,
                'in' => 'path',
                'required' => true,
                'schema' => ['type' => 'string'],
            ];
        }
        return $params;
    }

    private function responses(Api $api): array
    {
        $responses = [];

        if ($api->success_response) {
            $responses['200'] = $this->response($api->success_response);
        }
        if ($api->error_response) {
            $responses['400'] = $this->response($api->error_response);
        }

        return $responses ?: ['200' => ['description' => 'OK']];
    }

    private function response(string $body): array
    {
        $decoded = json_decode($body, true);
        $content = is_array($decoded)
            ? ['schema' => $this->schema($body), 'example' => $decoded]
            : ['example' => $body];

        return [
            'description' => 'Response',
            'content' => ['application/json' => $content],
        ];
    }

    private function schema(string $json): array
    {
        $decoded = json_decode($json, true);
        return $this->inferSchema($decoded);
    }

    private function inferSchema(mixed $value): array
    {
        if (is_array($value)) {
            if ($this->isList($value)) {
                return [
                    'type' => 'array',
                    'items' => $value ? $this->inferSchema($value[0]) : ['type' => 'object'],
                ];
            }
            $properties = [];
            foreach ($value as $key => $item) {
                $properties[$key] = $this->inferSchema($item);
            }
            return ['type' => 'object', 'properties' => $properties];
        }
        if (is_int($value)) return ['type' => 'integer'];
        if (is_float($value)) return ['type' => 'number'];
        if (is_bool($value)) return ['type' => 'boolean'];
        if ($value === null) return ['type' => 'string', 'nullable' => true];
        return ['type' => 'string'];
    }

    private function isList(array $arr): bool
    {
        return array_is_list($arr);
    }
}
