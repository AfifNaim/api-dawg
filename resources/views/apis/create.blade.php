@extends('layouts.app')

@section('title', 'Tambah API — API DAWG')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-2">Tambah API</h1>
    <p class="text-sm text-slate-500 mb-6">Project: <span class="font-semibold">{{ $project->name }}</span></p>
    @include('apis.form', ['project' => $project, 'api' => null, 'action' => '/manage/projects/' . $project->id . '/apis', 'method' => 'POST', 'preselectedGroup' => $preselectedGroup ?? null])
</div>
@endsection
