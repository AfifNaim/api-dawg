<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $project->groups()->create($data);
        return redirect("/manage")->with('status', 'Grup ditambahkan.');
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $group->update($data);
        return redirect("/manage")->with('status', 'Grup diperbarui.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $projectId = $group->project_id;
        $group->delete();
        return redirect("/manage")->with('status', 'Grup dihapus.');
    }
}
