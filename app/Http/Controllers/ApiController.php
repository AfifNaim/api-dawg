<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiController extends Controller
{
    public function create(Request $request, Project $project): View
    {
        return view('apis.create', [
            'project' => $project,
            'preselectedGroup' => $request->integer('group_id'),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request, $project);
        $project->apis()->create($data);
        return redirect("/manage")->with('status', 'API ditambahkan.');
    }

    public function edit(Project $project, Api $api): View
    {
        return view('apis.edit', ['project' => $project, 'api' => $api]);
    }

    public function update(Request $request, Project $project, Api $api): RedirectResponse
    {
        $data = $this->validated($request, $project);
        $api->update($data);
        return redirect("/manage")->with('status', 'API diperbarui.');
    }

    public function destroy(Project $project, Api $api): RedirectResponse
    {
        $api->delete();
        return redirect("/manage")->with('status', 'API dihapus.');
    }

    private function validated(Request $request, Project $project): array
    {
        return $request->validate([
            'group_id' => 'nullable|exists:groups,id,project_id,' . $project->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'endpoint' => 'required|string|max:255',
            'headers' => 'nullable|string',
            'request_json' => 'nullable|string',
            'example_request' => 'nullable|string',
            'success_response' => 'nullable|string',
            'error_response' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
    }
}
