<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        if ($api->request_json) {
            $decoded = json_decode($api->request_json, true);

            // Method yang tidak boleh membawa body (GET/HEAD/DELETE/OPTIONS)
            // -> jadikan request_json sebagai query parameters.
            if (in_array(strtoupper($api->method), ['GET', 'HEAD', 'DELETE', 'OPTIONS'], true)) {
                $queryParams = [];
                foreach ((array) $decoded as $key => $value) {
                    $queryParams[] = [
                        'name' => $key,
                        'in' => 'query',
                        'required' => false,
                        'schema' => $this->inferSchema($value),
                        'example' => $value,
                    ];
                }
                $parameters = array_merge($parameters ?? [], $queryParams);
            } else {
                $operation['requestBody'] = [
                    'required' => false,
                    'content' => [
                        'application/json' => [
                            'schema' => $this->schema($api->request_json),
                            'example' => $decoded,
                        ],
                    ],
                ];
            }
        }

        if ($parameters) {
            $operation['parameters'] = $parameters;
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

    /**
     * Proxy untuk Swagger UI "Try it out" (Execute).
     *
     * Swagger UI memanggil endpoint ini (bukan base_url langsung) sehingga
     * browser tidak perlu menjangkau host target yang mungkin hanya reachable
     * dari sisi server (mis. *.dparagon2.blog via Valet lokal). Request
     * diteruskan secara transparan ke $project->proxy_target + path + query,
     * lalu response dikembalikan apa adanya.
     */
    public function proxy(Project $project, Request $request, ?string $path = null): Response
    {
        $target = trim((string) ($project->proxy_target ?? ''));
        if ($target === '') {
            return response()->json(['error' => 'Project ini tidak memiliki proxy_target.'], 400);
        }

        // Path asli dikirim sebagai bagian URL proxy:
        //   /p/{id}/proxy/v3/reservation/extend/allowed-types
        // Route menangkap sisanya di parameter {path?}.
        $proxyPath = '/' . ltrim((string) $path, '/');

        $url = rtrim($target, '/') . $proxyPath;
        if ($request->getQueryString()) {
            $url .= '?' . $request->getQueryString();
        }

        $client = new \GuzzleHttp\Client([
            'verify' => false, // target lokal pakai self-signed cert (Valet)
            'timeout' => 30,
            'http_errors' => false,
        ]);

        // Teruskan header kecuali yang dikelola proxy / hop-by-hop.
        $forwardHeaders = [];
        $skip = [
            'host', 'content-length', 'connection', 'x-proxy-path',
            'x-forwarded-for', 'x-forwarded-host', 'x-forwarded-proto',
        ];
        foreach ($request->headers->all() as $name => $values) {
            if (in_array(strtolower($name), $skip, true)) {
                continue;
            }
            $forwardHeaders[$name] = $values[0];
        }
        // Pastikan Host sesuai target agar routing Valet (server_name) cocok.
        $forwardHeaders['Host'] = parse_url($target, PHP_URL_HOST);

        $guzzleRequest = new \GuzzleHttp\Psr7\Request(
            $request->method(),
            $url,
            $forwardHeaders,
            $request->getContent()
        );

        try {
            $response = $client->send($guzzleRequest);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Proxy gagal meneruskan request: ' . $e->getMessage(),
            ], 502);
        }

        // Teruskan response apa adanya (status, header relevan, body).
        $body = (string) $response->getBody();
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            if (in_array(strtolower($name), ['transfer-encoding', 'content-encoding', 'connection'], true)) {
                continue;
            }
            $headers[$name] = $values[0];
        }

        return response($body, $response->getStatusCode())
            ->withHeaders($headers);
    }
}
