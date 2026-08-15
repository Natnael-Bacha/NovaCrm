<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function createProject(StoreProjectRequest $request)
    {

        $this->authorize('create', Project::class);
        $validated = $request->validated();

        Project::create($validated);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function updateProject(UpdateProjectRequest $request, Project $project)
    {

        $this->authorize('update', $project);

        $validated = $request->validated();

        $project->update($validated);

        return redirect()->back()
            ->with('success', 'Project updated successfully.');
    }

    public function deleteProject(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully!');
    }
}
