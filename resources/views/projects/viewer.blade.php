@extends('layouts.app')

@section('title', $project->name . ' — API DAWG')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 py-4">
    <div class="flex items-center justify-between mb-3">
        <div>
            <a href="/" class="text-sm text-blue-600 hover:underline">&larr; Semua project</a>
            <h1 class="text-xl font-bold mt-1">{{ $project->name }}</h1>
            <p class="text-xs text-slate-500">{{ $project->description }}</p>
        </div>
        <div class="text-right text-xs text-slate-400">
            <div>Base URL (tampilan)</div>
            <div class="font-mono text-slate-600">{{ $project->base_url }}</div>
            @if($project->proxy_target)
                <div class="mt-1">Execute &rarr; <span class="font-mono text-emerald-600">{{ $project->proxy_target }}</span></div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4 items-start">
        {{-- KIRI: daftar API --}}
        <aside class="col-span-12 md:col-span-3 lg:col-span-2">
            <div class="bg-white border rounded-lg p-2 sticky top-2 max-h-[calc(100vh-2rem)] overflow-auto">
                <div class="text-xs font-semibold text-slate-500 px-2 py-1">Daftar API</div>
                <ul id="api-list" class="space-y-3">
                    @foreach($groupList as $group)
                        <li>
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-2 py-1">{{ $group['name'] }}</div>
                            <ul class="space-y-1">
                                @foreach($group['apis'] as $api)
                                    <li>
                                        <button type="button" data-api-id="{{ $api['id'] }}"
                                            class="api-item w-full text-left text-sm px-2 py-1.5 rounded hover:bg-slate-100 flex items-center gap-2">
                                            <span class="method-badge text-[10px] font-bold px-1.5 py-0.5 rounded {{ $api['method'] === 'GET' ? 'bg-emerald-100 text-emerald-700' : ($api['method'] === 'POST' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $api['method'] }}</span>
                                            <span class="truncate">{{ $api['name'] }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                    @if(count($ungrouped))
                        <li>
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-2 py-1">Lainnya</div>
                            <ul class="space-y-1">
                                @foreach($ungrouped as $api)
                                    <li>
                                        <button type="button" data-api-id="{{ $api['id'] }}"
                                            class="api-item w-full text-left text-sm px-2 py-1.5 rounded hover:bg-slate-100 flex items-center gap-2">
                                            <span class="method-badge text-[10px] font-bold px-1.5 py-0.5 rounded {{ $api['method'] === 'GET' ? 'bg-emerald-100 text-emerald-700' : ($api['method'] === 'POST' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $api['method'] }}</span>
                                            <span class="truncate">{{ $api['name'] }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </aside>

        {{-- TENGAH: request + output --}}
        <section class="col-span-12 md:col-span-6 lg:col-span-7">
            <div class="bg-white border rounded-lg p-4 min-h-[60vh]">
                <div id="api-detail">
                    <div class="text-slate-400 text-sm">Pilih API di kiri untuk melihat detail & mencoba.</div>
                </div>

                <div id="try-panel" class="hidden mt-4 border-t pt-4">
                    <div class="flex items-center gap-2 mb-2">
                        <button id="btn-execute" class="bg-blue-600 text-white text-sm font-medium px-3 py-1.5 rounded hover:bg-blue-700">Execute</button>
                        <span id="exec-status" class="text-xs text-slate-500"></span>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 mb-1">Response</div>
                        <pre id="exec-output" class="json-block bg-slate-900 text-slate-100 rounded p-3 max-h-96 overflow-auto"></pre>
                    </div>
                </div>
            </div>
        </section>

        {{-- KANAN: contoh response --}}
        <aside class="col-span-12 md:col-span-3 lg:col-span-3">
            <div class="space-y-3 sticky top-2">
                <div class="bg-white border rounded-lg p-3">
                    <div class="text-xs font-semibold text-emerald-600 mb-1">Contoh Sukses (200)</div>
                    <pre id="sample-success" class="json-block text-slate-700 bg-slate-50 rounded p-2 max-h-72 overflow-auto">—</pre>
                </div>
                <div class="bg-white border rounded-lg p-3">
                    <div class="text-xs font-semibold text-rose-600 mb-1">Contoh Gagal</div>
                    <pre id="sample-error" class="json-block text-slate-700 bg-slate-50 rounded p-2 max-h-72 overflow-auto">—</pre>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
window.API_DATA = @json($apiList);

window.PROJECT = {
    id: @json($project->id),
    token: @json($project->token ?? ''),
    proxyTarget: @json($project->proxy_target ?? ''),
};
</script>
<script>
(function () {
    const apiData = window.API_DATA;
    const project = window.PROJECT;
    const detailEl = document.getElementById('api-detail');
    const tryPanel = document.getElementById('try-panel');
    const sampleSuccess = document.getElementById('sample-success');
    const sampleError = document.getElementById('sample-error');
    const execOutput = document.getElementById('exec-output');
    const execStatus = document.getElementById('exec-status');
    let selected = null;

    function esc(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function renderFields(api, kind) {
        // kind: 'query' | 'body'
        if (kind === 'body' && api.body) {
            return `<textarea data-field="body" rows="8" class="w-full font-mono text-xs border rounded p-2">${esc(JSON.stringify(api.body, null, 2))}</textarea>`;
        }
        const list = kind === 'query' ? api.query : api.params;
        if (!list || !list.length) return '<div class="text-xs text-slate-400">—</div>';
        return list.map(p => {
            const val = (api.body && api.body[p.name] !== undefined) ? api.body[p.name] : (p.example ?? '');
            const ph = p.example !== undefined && p.example !== '' ? `placeholder="${esc(JSON.stringify(p.example))}"` : '';
            return `<label class="block mb-2">
                <span class="text-xs text-slate-500">${esc(p.name)}${p.required ? ' <span class="text-rose-500">*</span>' : ''} <span class="text-slate-400">(${esc(p.in)})</span></span>
                <input data-field="${kind}" data-name="${esc(p.name)}" value="${esc(val ?? '')}" ${ph} class="w-full border rounded px-2 py-1 text-sm">
            </label>`;
        }).join('');
    }

    function selectApi(id) {
        selected = apiData.find(a => a.id === id);
        if (!selected) return;

        document.querySelectorAll('.api-item').forEach(b => {
            b.classList.toggle('bg-blue-50', b.dataset.apiId == id);
            b.classList.toggle('font-medium', b.dataset.apiId == id);
        });

        const methodColor = selected.method === 'GET' ? 'text-emerald-700' : selected.method === 'POST' ? 'text-blue-700' : 'text-amber-700';
        detailEl.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold ${methodColor}">${esc(selected.method)}</span>
                <span class="font-mono text-sm">${esc(selected.endpoint)}</span>
            </div>
            <p class="text-sm text-slate-600 mt-1">${esc(selected.description ?? '')}</p>
            <div class="mt-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">Path Parameters</div>
                ${renderFields(selected, 'params')}
                <div class="text-xs font-semibold text-slate-500 mb-1 mt-2">Query Parameters</div>
                ${renderFields(selected, 'query')}
                <div class="text-xs font-semibold text-slate-500 mb-1 mt-2">Request Body (JSON)</div>
                ${renderFields(selected, 'body')}
            </div>`;

        sampleSuccess.textContent = selected.success ? pretty(selected.success) : '—';
        sampleError.textContent = selected.error ? pretty(selected.error) : '—';
        execOutput.textContent = '';
        execStatus.textContent = '';
        tryPanel.classList.remove('hidden');
    }

    function pretty(raw) {
        if (!raw) return '—';
        try {
            const obj = typeof raw === 'string' ? JSON.parse(raw) : raw;
            return JSON.stringify(obj, null, 2);
        } catch (e) {
            return String(raw);
        }
    }

    function buildUrlAndBody() {
        let path = selected.endpoint;
        // ganti path params
        document.querySelectorAll('#api-detail [data-field="params"]').forEach(inp => {
            const name = inp.dataset.name;
            const val = inp.value;
            path = path.replace('{' + name + '}', encodeURIComponent(val));
        });
        // query params
        const qs = [];
        document.querySelectorAll('#api-detail [data-field="query"]').forEach(inp => {
            if (inp.value !== '') qs.push(encodeURIComponent(inp.dataset.name) + '=' + encodeURIComponent(inp.value));
        });
        const target = project.proxyTarget || '';
        let url;
        if (target) {
            url = '/p/' + project.id + '/proxy' + path + (qs.length ? '?' + qs.join('&') : '');
        } else {
            url = window.location.origin + path + (qs.length ? '?' + qs.join('&') : '');
        }
        // body
        let body = null;
        const bodyEl = document.querySelector('#api-detail [data-field="body"]');
        if (bodyEl && selected.method !== 'GET') {
            try { body = bodyEl.value.trim() ? JSON.parse(bodyEl.value) : null; }
            catch (e) { body = bodyEl.value; }
        }
        return { url, body };
    }

    async function execute() {
        if (!selected) return;
        execStatus.textContent = 'mengirim…';
        execOutput.textContent = '';
        const { url, body } = buildUrlAndBody();
        const headers = { 'Accept': 'application/json' };
        if (selected.method !== 'GET' && body !== null) headers['Content-Type'] = 'application/json';
        if (project.token) headers['Authorization'] = 'Bearer ' + project.token;

        try {
            const res = await fetch(url, {
                method: selected.method,
                headers,
                body: (selected.method !== 'GET' && body !== null) ? JSON.stringify(body) : undefined,
            });
            const text = await res.text();
            let shown = text;
            try { shown = JSON.stringify(JSON.parse(text), null, 2); } catch (e) {}
            execOutput.textContent = 'HTTP ' + res.status + '\n\n' + shown;
            execStatus.textContent = 'HTTP ' + res.status;
            execStatus.className = 'text-xs ' + (res.ok ? 'text-emerald-600' : 'text-rose-600');
        } catch (e) {
            execOutput.textContent = 'Error: ' + e.message;
            execStatus.textContent = 'gagal';
            execStatus.className = 'text-xs text-rose-600';
        }
    }

    document.querySelectorAll('.api-item').forEach(b => {
        b.addEventListener('click', () => selectApi(parseInt(b.dataset.apiId, 10)));
    });
    document.getElementById('btn-execute').addEventListener('click', execute);

    // pilih API pertama secara default
    if (apiData.length) selectApi(apiData[0].id);
})();
</script>
@endsection
