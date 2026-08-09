<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Auth as SupportFacadesAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{










public function changeSupervisors(Request $request)
{
    $validated = $request->validate([
        'old_supervisor' => 'required|exists:users,id',
        'new_supervisor' => 'required|exists:users,id',
        'new_role' => 'required|in:admin,agent,collector',
    ]);

    User::where('supervisor_id', $validated['old_supervisor'])
        ->update([
            'supervisor_id' => $validated['new_supervisor']
        ]);

    User::findOrFail($validated['old_supervisor'])
        ->update([
            'role' => $validated['new_role']
        ]);

    return redirect()
        ->route('team.index')
        ->with('success', 'Agents reassigned and role updated.');
}


public function getProjectData($id)
{
    $project = Project::find($id);
    
    
    if (!$project) {
        return response()->json(['error' => 'Project not found'], 404);
    }
    
    return response()->json($project);
}













// public function updateLeadStage(Request $request, $id)
// {
//     try {
//         $lead = Lead::findOrFail($id);
//         $lead->current_stage = $request->stage;
//         $lead->save();

//         return response()->json([
//             'success' => true,
//             'message' => 'Lead stage updated successfully'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to update lead stage: ' . $e->getMessage()
//         ], 500);
//     }
// }

 public function createAction(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'activity_type' => [
                'required',
                'in:follow_up_call,meeting,property_visit,email'
            ],

            'assigned_to' => [
                'required',
                'exists:users,id'
            ],

            'status' => [
                'required',
                'in:done,on_progress'
            ],

            'scheduled_time' => [
                'required',
                'date',
                'after_or_equal:today'
            ],

            'description' => [
                'nullable',
                'string'
            ],
        ]);


        Action::create([
            'lead_id' => $lead->id,
            'activity_type' => $validated['activity_type'],
            'assigned_to' => $validated['assigned_to'],
            'status' => $validated['status'],
            'scheduled_time' => $validated['scheduled_time'],
            'description' => $validated['description'],
        ]);


        return redirect()
            ->back()
            ->with('success', 'Action created successfully');
    }

    public function updateAction(Request $request, Action $action)
{
    $validated = $request->validate([
        'lead_id'        => 'required|exists:leads,id',
        'activity_type'  => 'required|in:follow_up_call,meeting,property_visit,email',
        'assigned_to'    => 'required|exists:users,id',
        'status'         => 'required|in:done,on_progress',
        'scheduled_time' => 'required|date|after_or_equal:today',
        'description'    => 'nullable|string',
    ]);

    $action->update([
        'lead_id'        => $validated['lead_id'],
        'activity_type'  => $validated['activity_type'],
        'assigned_to'    => $validated['assigned_to'],
        'status'         => $validated['status'],
        'scheduled_time' => $validated['scheduled_time'],
        'description'    => $validated['description'] ?? null,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Action updated successfully.');
} 

   public function deleteAction(Action $action)
{
    $action->delete();

    return redirect()
        ->back()
        ->with('success', 'Action deleted successfully.');
}


public function updateActionActivity(Request $request, Action $action)
{
    $validated = $request->validate([
        'activity_type' => 'required|in:follow_up_call,meeting,property_visit,email',
    ]);

    $action->update([
        'activity_type' => $validated['activity_type'],
    ]);

    return redirect()
        ->back()
        ->with('success', 'Activity type updated successfully.');
}

public function updateActionStatus(Request $request, Action $action)
{
    $validated = $request->validate([
        'status' => 'required|in:done,on_progress',
    ]);

    $action->update([
        'status' => $validated['status'],
    ]);

    return redirect()
        ->back()
        ->with('success', 'Status updated successfully.');
}

}




