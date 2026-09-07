<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaygroundController extends Controller
{
    public function send(Request $request, Api $api): JsonResponse
    {
        $request->validate([
            'base_url' => 'required|url',
            'body' => 'nullable|string',
        ]);

        $url = rtrim($request->input('base_url'), '/') . '/' . ltrim($api->endpoint, '/');
        $body = $request->input('body');

        $client = Http::timeout(15);

        if ($token = $api->project->token) {
            $client = $client->withToken($token);
        }

        try {
            $response = match ($api->method) {
                'GET' => $client->get($url),
                'POST' => $client->post($url, $this->parseBody($body)),
                'PUT' => $client->put($url, $this->parseBody($body)),
                'PATCH' => $client->patch($url, $this->parseBody($body)),
                'DELETE' => $client->delete($url),
                default => $client->get($url),
            };

            return response()->json([
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (ConnectionException $e) {
            return response()->json([
                'status' => 0,
                'body' => 'Gagal terhubung: ' . $e->getMessage(),
            ]);
        }
    }

    private function parseBody(?string $body): array
    {
        if (!$body) {
            return [];
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }
}
