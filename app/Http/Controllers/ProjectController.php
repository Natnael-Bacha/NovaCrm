<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{

 public function createProject(Request $request)
{

  $request->merge(
    collect($request->all())
        ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
        ->toArray()
);
    $validated = $request->validate([
        'project_name' => [
            'required',
            'string',
            'min:3',
            Rule::unique('projects', 'project_name'),
        ],
        'project_manager' => ['required', 'string', 'min:3'],
        'location_address' => ['required', 'string', 'min:3'],
        'total_floors' => ['required', 'integer', 'min:1'],
        'completed_floors' => ['required', 'integer', 'min:0'],
        'total_units' => ['required', 'integer', 'min:1'],
        'due_date' => ['required', 'date'],
    ]);

    if ($validated['completed_floors'] > $validated['total_floors']) {
    return back()
        ->withErrors([
            'completed_floors' => 'Completed floors cannot exceed total floors.'
        ])
        ->withInput();
}

    Project::create($validated);

    return redirect()->back()->with('success', 'Project created successfully.');
}

}


