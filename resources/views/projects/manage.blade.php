@extends('layouts.app')

@section('title', 'Kelola — API DAWG')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">Kelola Project</h1>
            <p class="text-sm text-slate-500">Kelola project, grup, dan API dokumentasi.</p>
        </div>
        <a href="/manage/projects/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm">+ Project Baru</a>
    </div>

    @if (session('status'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 mb-6 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('status') }}
        </div>
    @endif

    @forelse ($projects as $project)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
            {{-- Project header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="min-w-0">
                    <h2 class="font-bold text-lg truncate">{{ $project->name }}</h2>
                    <p class="text-xs text-slate-400 font-mono truncate">{{ $project->base_url }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="/p/{{ $project->id }}" class="text-slate-600 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100" title="Lihat dokumentasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="/manage/projects/{{ $project->id }}/edit" class="text-slate-600 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100" title="Edit project">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="/manage/projects/{{ $project->id }}" onsubmit="return confirm('Hapus project ini beserta semua API-nya?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-600 hover:text-red-600 p-2 rounded-lg hover:bg-red-50" title="Hapus project">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-3">
                <button onclick="toggleForm('addGroup{{ $project->id }}')" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-blue-600 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-blue-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Grup
                </button>
                <a href="/manage/projects/{{ $project->id }}/apis/create" class="inline-flex items-center gap-1.5 text-xs font-medium text-white bg-slate-800 hover:bg-slate-900 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    API
                </a>
            </div>

            {{-- Add group form (hidden) --}}
            <div id="addGroup{{ $project->id }}" class="hidden px-6 py-3 bg-slate-50 border-b border-slate-100">
                <form method="POST" action="/manage/projects/{{ $project->id }}/groups" class="flex items-center gap-2">
                    @csrf
                    <input name="name" placeholder="Nama grup baru" required class="flex-1 text-sm border rounded-lg px-3 py-2">
                    <button class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                    <button type="button" onclick="toggleForm('addGroup{{ $project->id }}')" class="text-sm text-slate-500 px-2">Batal</button>
                </form>
            </div>

            {{-- Groups --}}
            <div class="px-6 py-4 space-y-5">
                @forelse ($project->groups as $group)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-4 rounded bg-blue-500"></span>
                                <span class="font-semibold text-sm text-slate-700">{{ $group->name }}</span>
                                <span class="text-xs text-slate-400">{{ $group->apis->count() }} API</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="/manage/projects/{{ $project->id }}/apis/create?group_id={{ $group->id }}" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 px-2 py-1 rounded-lg hover:bg-blue-50" title="Tambah API di grup ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    API
                                </a>
                                <button onclick="toggleForm('renameGroup{{ $group->id }}')" class="text-slate-400 hover:text-blue-600 p-1.5 rounded hover:bg-slate-100" title="Ubah nama">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="/manage/groups/{{ $group->id }}" onsubmit="return confirm('Hapus grup ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-slate-400 hover:text-red-600 p-1.5 rounded hover:bg-red-50" title="Hapus grup">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Rename form --}}
                        <div id="renameGroup{{ $group->id }}" class="hidden mb-2">
                            <form method="POST" action="/manage/groups/{{ $group->id }}" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <input name="name" value="{{ $group->name }}" required class="flex-1 text-sm border rounded-lg px-3 py-1.5">
                                <button class="bg-blue-600 text-white text-sm px-3 py-1.5 rounded-lg">Simpan</button>
                                <button type="button" onclick="toggleForm('renameGroup{{ $group->id }}')" class="text-sm text-slate-500 px-2">Batal</button>
                            </form>
                        </div>

                        <ul class="space-y-1">
                            @foreach ($group->apis as $gapi)
                                @include('projects.api_row', ['project' => $project, 'api' => $gapi])
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada grup. Klik <span class="font-medium text-slate-500">+ Grup</span> untuk membuat.</p>
                @endforelse

                @php $ungrouped = $project->apis->whereNull('group_id'); @endphp
                @if ($ungrouped->isNotEmpty())
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-4 rounded bg-slate-300"></span>
                                <span class="font-semibold text-sm text-slate-700">Tanpa Grup</span>
                            </div>
                            <a href="/manage/projects/{{ $project->id }}/apis/create" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 px-2 py-1 rounded-lg hover:bg-blue-50" title="Tambah API">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                API
                            </a>
                        </div>
                        <ul class="space-y-1">
                            @foreach ($ungrouped as $uapi)
                                @include('projects.api_row', ['project' => $project, 'api' => $uapi])
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-500 mb-4">Belum ada project.</p>
            <a href="/manage/projects/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">Buat project pertama</a>
        </div>
    @endforelse
</div>

<script>
function toggleForm(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>
@endsection
