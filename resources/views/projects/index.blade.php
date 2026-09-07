@extends('layouts.app')

@section('title', 'Dokumentasi API')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-2">🎤 Dokumentasi API</h1>
    <p class="text-slate-500 mb-8">Pilih project untuk melihat dokumentasi API-nya.</p>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($projects as $project)
            <a href="/p/{{ $project->id }}" class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <h2 class="font-bold text-lg mb-1">{{ $project->name }}</h2>
                <p class="text-xs text-slate-400 font-mono mb-2">{{ $project->base_url }}</p>
                <p class="text-sm text-slate-500 line-clamp-2">{{ $project->description }}</p>
                <p class="text-xs text-slate-400 mt-3">{{ $project->apis_count }} API</p>
            </a>
        @empty
            <p class="text-slate-400 col-span-full">Belum ada project. <a href="/manage" class="text-blue-600">Kelola di sini</a>.</p>
        @endforelse
    </div>
</div>
@endsection
