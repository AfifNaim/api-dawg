<li class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 group">
    <span class="text-[10px] font-bold w-14 text-center px-1.5 py-1 rounded-md shrink-0 {{ $api->method === 'GET' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $api->method }}</span>
    <div class="min-w-0 flex-1">
        <div class="text-sm font-medium text-slate-700 truncate">{{ $api->name }}</div>
        <div class="text-xs text-slate-400 font-mono truncate">{{ $api->endpoint }}</div>
    </div>
    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
        <a href="/manage/projects/{{ $project->id }}/apis/{{ $api->id }}/edit" class="text-slate-400 hover:text-blue-600 p-1.5 rounded hover:bg-slate-100" title="Edit API">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </a>
        <form method="POST" action="/manage/projects/{{ $project->id }}/apis/{{ $api->id }}" onsubmit="return confirm('Hapus API ini?')">
            @csrf @method('DELETE')
            <button class="text-slate-400 hover:text-red-600 p-1.5 rounded hover:bg-red-50" title="Hapus API">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </form>
    </div>
</li>
