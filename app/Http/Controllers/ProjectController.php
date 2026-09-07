<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('projects.index', ['projects' => Project::withCount('apis')->get()]);
    }

    public function manage(): View
    {
        return view('projects.manage', ['projects' => Project::with('groups')->get()]);
    }

    public function show(Project $project): View
    {
        return view('projects.swagger', ['project' => $project]);
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'token' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        Project::create($data);
        return redirect('/manage')->with('status', 'Project dibuat.');
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'token' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        $project->update($data);
        return redirect('/manage')->with('status', 'Project diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        return redirect('/manage')->with('status', 'Project dihapus.');
    }
}
