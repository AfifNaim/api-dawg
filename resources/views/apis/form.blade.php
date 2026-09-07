@if ($errors->any())
    <div class="bg-red-100 text-red-700 border border-red-200 rounded px-4 py-2 mb-4 text-sm">
        <ul class="list-disc ml-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="bg-white rounded-xl shadow p-6 space-y-4">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Nama API</label>
            <input name="name" value="{{ old('name', $api->name ?? '') }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Method</label>
            <select name="method" class="w-full border rounded px-3 py-2">
                @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m)
                    <option value="{{ $m }}" {{ old('method', $api->method ?? 'GET') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Endpoint</label>
            <input name="endpoint" value="{{ old('endpoint', $api->endpoint ?? '') }}" required placeholder="/api/users" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Grup</label>
            <select name="group_id" class="js-select2 w-full border rounded px-3 py-2">
                <option value="">Tanpa Grup</option>
                @foreach ($project->groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $api->group_id ?? $preselectedGroup ?? '') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Headers (JSON)</label>
        <textarea name="headers" rows="2" placeholder='{"Authorization": "Bearer <token>"}' class="js-json-editor w-full font-mono text-sm border rounded px-3 py-2">{{ old('headers', $api->headers ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description', $api->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Request JSON</label>
        <textarea name="request_json" rows="5" placeholder='{"email": "user@example.com"}' class="js-json-editor w-full font-mono text-sm border rounded px-3 py-2">{{ old('request_json', $api->request_json ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Contoh Request (curl / lengkap)</label>
        <textarea name="example_request" rows="4" class="js-json-editor w-full font-mono text-sm border rounded px-3 py-2">{{ old('example_request', $api->example_request ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Contoh Response — Sukses</label>
        <textarea name="success_response" rows="6" class="js-json-editor w-full font-mono text-sm border rounded px-3 py-2">{{ old('success_response', $api->success_response ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Contoh Response — Error</label>
        <textarea name="error_response" rows="6" class="js-json-editor w-full font-mono text-sm border rounded px-3 py-2">{{ old('error_response', $api->error_response ?? '') }}</textarea>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
</form>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.querySelectorAll('.js-select2').forEach(function (el) {
    $(el).select2({ width: '100%' });
});
</script>
<script>
document.querySelectorAll('.js-json-editor').forEach(function (textarea) {
    var editor = CodeMirror.fromTextArea(textarea, {
        mode: { name: 'javascript', json: true },
        lineNumbers: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        lineWrapping: true,
    });
    editor.on('change', function () { editor.save(); });
});
</script>
