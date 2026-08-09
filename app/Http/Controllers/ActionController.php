<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionRequest;
use App\Http\Requests\UpdateActionActivityRequest;
use App\Http\Requests\UpdateActionRequest;
use App\Http\Requests\UpdateActionStatusRequest;
use App\Models\Action;
use App\Models\Lead;

class ActionController extends Controller
{
    public function createAction(StoreActionRequest $request, Lead $lead)
    {   
        $this->authorize('create', Action::class);
        $validated = $request->validated();


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

        public function updateAction(UpdateActionRequest $request, Action $action)
{      
        $this->authorize('update', $action);
         $validated = $request->validated();

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
    $this->authorize('delete', $action);
    $action->delete();

    return redirect()
        ->back()
        ->with('success', 'Action deleted successfully.');
}

public function updateActionActivity(UpdateActionActivityRequest $request, Action $action)
{   
    $this->authorize('update', $action);
    $validated = $request->validated();

    $action->update([
        'activity_type' => $validated['activity_type'],
    ]);

    return redirect()
        ->back()
        ->with('success', 'Activity type updated successfully.');
}

public function updateActionStatus(UpdateActionStatusRequest $request, Action $action)
{   
    $this->authorize('update', $action);
    $validated = $request->validated();

    $action->update([
        'status' => $validated['status'],
    ]);

    return redirect()
        ->back()
        ->with('success', 'Status updated successfully.');
}

}
