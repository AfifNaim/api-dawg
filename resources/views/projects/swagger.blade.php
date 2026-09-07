@extends('layouts.app')

@section('title', $project->name . ' — API DAWG')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <a href="/" class="text-sm text-blue-600 hover:underline">&larr; Semua project</a>
            <h1 class="text-2xl font-bold mt-1">{{ $project->name }}</h1>
            <p class="text-sm text-slate-500">{{ $project->description }}</p>
        </div>
        <div class="text-right text-xs text-slate-400">
            <div>Base URL</div>
            <div class="font-mono text-slate-600">{{ $project->base_url }}</div>
            <a href="/p/{{ $project->id }}/openapi" target="_blank" class="text-blue-600 hover:underline">openapi.json</a>
        </div>
    </div>

    <div id="swagger-ui"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
window.onload = function () {
    var projectToken = @json($project->token ?? '');

    window.ui = SwaggerUIBundle({
        url: '/p/{{ $project->id }}/openapi',
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [SwaggerUIBundle.presets.apis],
        layout: 'BaseLayout',
        onComplete: function () {
            // Jika project punya token, otomatis isi skema bearerAuth supaya
            // "Try it out" langsung terkirim Authorization: Bearer <token>.
            if (projectToken) {
                try {
                    ui.authActions.authorize({
                        bearerAuth: { name: 'bearerAuth', value: projectToken, schema: { type: 'http', scheme: 'bearer' } }
                    });
                } catch (e) {
                    console.warn('Auto-authorize gagal:', e);
                }
            }
        },
    });
};
</script>
@endsection
