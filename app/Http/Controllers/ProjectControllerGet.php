<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectControllerGet extends Controller
{
     public function getProjects(){
    
    $this->authorize('viewAny', Project::class);

    $projects = Project::all();

    return view('admin.projects', compact('projects'));
 }

 public function edit(Project $project)
{
    $this->authorize('view', $project); 
    
    return response()->json($project);
}
}
