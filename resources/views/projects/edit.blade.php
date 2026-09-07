@extends('layouts.app')

@section('title', 'Edit Project — API DAWG')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6">Edit Project</h1>
    @include('projects.form', ['project' => $project, 'action' => '/manage/projects/' . $project->id, 'method' => 'PUT'])
</div>
@endsection
