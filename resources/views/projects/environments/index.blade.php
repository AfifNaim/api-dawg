@extends('layouts.app')

@section('title', 'Environments — ' . $project->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <a href="/manage" class="text-sm text-blue-600 hover:underline">&larr; Kelola</a>
            <h1 class="text-xl font-bold mt-1">Environments — {{ $project->name }}</h1>
            <p class="text-xs text-slate-500">Set base URL, token, dan proxy target per environment (Development / Production / dst).</p>
        </div>
        <a href="/manage/projects/{{ $project->id }}/environments/create" class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">+ Environment</a>
    </div>

    @if(session('status'))
        <div class="bg-emerald-100 text-emerald-700 border border-emerald-200 rounded px-3 py-2 mb-3 text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-2">
        @forelse ($environments as $env)
            <div class="bg-white border rounded-lg p-4 flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ $env->name }} @if($env->is_default)<span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded ml-1">default</span>@endif</div>
                    <div class="text-xs text-slate-500 font-mono">{{ $env->base_url }}</div>
                    <div class="text-xs text-slate-400">proxy: {{ $env->proxy_target ?? '—' }} · token: {{ $env->token ? 'ada' : 'kosong' }}</div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <a href="/manage/projects/{{ $project->id }}/environments/{{ $env->id }}/edit" class="text-blue-600 hover:underline">Edit</a>
                    <form method="POST" action="/manage/projects/{{ $project->id }}/environments/{{ $env->id }}" onsubmit="return confirm('Hapus environment ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-500 text-sm">Belum ada environment.</p>
        @endforelse
    </div>
</div>
@endsection
