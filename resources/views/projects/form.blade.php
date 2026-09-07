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

    <div>
        <label class="block text-sm font-semibold mb-1">Nama Project</label>
        <input name="name" value="{{ old('name', $project->name ?? '') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Base URL</label>
        <input name="base_url" value="{{ old('base_url', $project->base_url ?? '') }}" required placeholder="https://api.example.com" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Bearer Token (opsional)</label>
        <input name="token" value="{{ old('token', $project->token ?? '') }}" placeholder="eyJhbGciOi..." class="w-full border rounded px-3 py-2">
        <p class="text-xs text-slate-400 mt-1">Dipakai otomatis sebagai <code>Authorization: Bearer &lt;token&gt;</code> di semua endpoint.</p>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $project->description ?? '') }}</textarea>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
</form>
