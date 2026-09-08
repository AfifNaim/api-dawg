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
        $project->load(['groups.apis', 'apis' => function ($q) {
            $q->whereNull('group_id');
        }]);

        $apis = $project->groups->flatMap->apis->merge($project->apis)->sortBy('sort_order')->values();

        $apiList = $apis->map(function ($a) {
            return [
                'id' => $a->id,
                'method' => $a->method,
                'name' => $a->name,
                'description' => $a->description,
                'endpoint' => $a->endpoint,
                'params' => $a->pathParams(),
                'query' => $a->queryParams(),
                'body' => $a->request_json ? json_decode($a->request_json, true) : null,
                'success' => $a->success_response,
                'error' => $a->error_response,
            ];
        })->values()->all();

        // Struktur sidebar: tiap group berisi list API-nya.
        $groupList = $project->groups->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'apis' => $g->apis->sortBy('sort_order')->values()->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'method' => $a->method,
                        'name' => $a->name,
                    ];
                })->all(),
            ];
        })->values()->all();

        // API tanpa group (jika ada)
        $ungrouped = $project->apis->whereNull('group_id')->sortBy('sort_order')->values()->map(function ($a) {
            return ['id' => $a->id, 'method' => $a->method, 'name' => $a->name];
        })->all();

        return view('projects.viewer', [
            'project' => $project,
            'apis' => $apis,
            'apiList' => $apiList,
            'groupList' => $groupList,
            'ungrouped' => $ungrouped,
        ]);
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
