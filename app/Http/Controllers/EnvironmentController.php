<?php

namespace App\Http\Controllers;

use App\Models\Environment;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnvironmentController extends Controller
{
    public function index(Project $project): View
    {
        return view('projects.environments.index', [
            'project' => $project,
            'environments' => $project->environments()->orderBy('sort_order')->get(),
        ]);
    }

    public function create(Project $project): View
    {
        return view('projects.environments.form', [
            'project' => $project,
            'environment' => null,
            'action' => route('environments.store', $project),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'token' => 'nullable|string',
            'proxy_target' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['project_id'] = $project->id;
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            $project->environments()->where('is_default', true)->update(['is_default' => false]);
        }

        $project->environments()->create($data);

        return redirect()->route('environments.index', $project)->with('status', 'Environment ditambahkan.');
    }

    public function edit(Project $project, Environment $environment): View
    {
        return view('projects.environments.form', [
            'project' => $project,
            'environment' => $environment,
            'action' => route('environments.update', [$project, $environment]),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Project $project, Environment $environment): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'token' => 'nullable|string',
            'proxy_target' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            $project->environments()->where('is_default', true)->where('id', '!=', $environment->id)->update(['is_default' => false]);
        }

        $environment->update($data);

        return redirect()->route('environments.index', $project)->with('status', 'Environment diperbarui.');
    }

    public function destroy(Project $project, Environment $environment): RedirectResponse
    {
        $environment->delete();
        return redirect()->route('environments.index', $project)->with('status', 'Environment dihapus.');
    }
}
