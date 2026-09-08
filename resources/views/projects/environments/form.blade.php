@extends('layouts.app')

@section('title', ($environment ? 'Edit' : 'Buat') . ' Environment — ' . $project->name)

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-xl font-bold mb-4">{{ $environment ? 'Edit' : 'Buat' }} Environment</h1>
    <p class="text-xs text-slate-500 mb-4">Project: <strong>{{ $project->name }}</strong></p>

    <form method="POST" action="{{ $action }}" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf
        @if ($method === 'PUT') @method('PUT') @endif

        <div>
            <label class="block text-sm font-semibold mb-1">Nama Environment</label>
            <input name="name" value="{{ old('name', $environment->name ?? '') }}" required placeholder="Development / Production" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Base URL (tampilan)</label>
            <input name="base_url" value="{{ old('base_url', $environment->base_url ?? '') }}" required placeholder="https://api.example.com" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Bearer Token (opsional)</label>
            <input name="token" value="{{ old('token', $environment->token ?? '') }}" placeholder="eyJhbGciOi..." class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Proxy Target (opsional)</label>
            <input name="proxy_target" value="{{ old('proxy_target', $environment->proxy_target ?? '') }}" placeholder="https://api.internal.blog" class="w-full border rounded px-3 py-2">
            <p class="text-xs text-slate-400 mt-1">Host asli yang dipakai saat Execute (bila base_url hanya untuk tampilan). Kosongkan = Execute langsung ke base_url.</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $environment->is_default ?? false) ? 'checked' : '' }} id="is_default">
            <label for="is_default" class="text-sm">Jadikan default</label>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Urutan (sort)</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $environment->sort_order ?? 0) }}" class="w-24 border rounded px-3 py-2">
        </div>
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            <a href="/manage/projects/{{ $project->id }}/environments" class="text-slate-500 px-3 py-2">Batal</a>
        </div>
    </form>
</div>
@endsection
