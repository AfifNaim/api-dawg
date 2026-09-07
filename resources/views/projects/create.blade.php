@extends('layouts.app')

@section('title', 'Buat Project — API DAWG')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6">Buat Project</h1>
    @include('projects.form', ['project' => null, 'action' => '/manage/projects', 'method' => 'POST'])
</div>
@endsection
