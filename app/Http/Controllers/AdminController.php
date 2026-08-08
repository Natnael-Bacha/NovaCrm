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





public function createUnit(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'unit_number' => 'required|string|max:255|unique:units,unit_number',
        'floor' => 'required|integer',
        'unit_type' => 'required|in:apartment,penthouse,office_space,commercial,studio,duplex',
        'size' => 'required|numeric',
        'price' => 'required|numeric',
        'status' => 'required|in:available,reserved,sold',
    ]);


    Unit::create($validated);


    return redirect()
        ->back()
        ->with('success', 'Unit created successfully.');
}


public function updateUnit(Request $request, $id)
{
    $request->merge(
        collect($request->all())
            ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
            ->toArray()
    );

    abort_if(Auth::user()->role !== 'admin', 403);

    $unit = Unit::findOrFail($id);

    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'unit_number' => [
            'required',
            'string',
            'max:255',
            Rule::unique('units')
                ->where(fn ($query) => $query->where('project_id', $request->project_id))
                ->ignore($unit->id),
        ],
        'floor' => 'required|integer|min:1',
        'unit_type' => 'required|in:apartment,penthouse,office_space,commercial,studio,duplex',
        'size' => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',
        'status' => 'required|in:available,reserved,sold',
    ]);

    $project = Project::findOrFail($validated['project_id']);

    if ($validated['floor'] > $project->total_floors) {
        return back()
            ->withErrors([
                'floor' => 'The selected floor exceeds the total floors of the project.'
            ])
            ->withInput();
    }

    $unit->update($validated);

    return back()->with('success', 'Unit updated successfully.');
}

public function deleteUnit($id)
{   
    abort_if(Auth::user()->role !== 'admin', 403);
    $unit = Unit::findorFail($id);
    
    if (!$unit){
            return redirect()->back()->with('error', 'Project not found');
    }
    
    $unit->delete();
    
    return redirect()->back()->with('success', 'Unit deleted successfully!');
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


public function createDeal(Request $request, Lead $lead)
{
    abort_if(Auth::user()->role !== 'admin', 403);

    if ($lead->deals()->exists()) {
        return back()->withErrors([
            'lead' => 'This lead already has a deal.'
        ]);
    }

    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'required|exists:units,id',
        'collector_id' => 'required|exists:users,id',

        'deal_amount' => 'required|numeric|min:0',
        'down_payment' => 'required|numeric|min:0',

        'payment_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
        'number_of_installments' => 'required|integer|min:1',

        'start_date' => 'required|date',

        'commission_type' => 'required|in:percentage,fixed_amount',
        'commission_value' => 'required|numeric|min:0',

        'beneficiary' => 'required|in:internal_agent,external_agent',

        'commission_trigger' => 'required|in:immediate,each_payment,full_payment',
    ]);



    $validated['lead_id'] = $lead->id;


    
    if ($validated['down_payment'] > $validated['deal_amount']) {
        return back()->withErrors([
            'down_payment' => 'Down payment cannot exceed deal amount.'
        ]);
    }


  
    $unit = Unit::findOrFail($validated['unit_id']);



    if ($unit->status !== 'available') {
        return back()->withErrors([
            'unit_id' => 'This unit is not available.'
        ]);
    }


  
    $remainingAmount = 
        $validated['deal_amount'] - $validated['down_payment'];


    $validated['installment_amount'] = $remainingAmount > 0
        ? $remainingAmount / $validated['number_of_installments']
        : 0;



    if ($validated['down_payment'] == 0) {

        $validated['payment_status'] = 'pending';

    } elseif ($validated['down_payment'] >= $validated['deal_amount']) {

        $validated['payment_status'] = 'fully_paid';

    } else {

        $validated['payment_status'] = 'partial_payment';

    }


    DB::transaction(function () use ($validated, $lead, $unit) {


        Deal::create($validated);



        if ($validated['payment_status'] === 'fully_paid') {


            $lead->update([
                'current_stage' => 'completed',
            ]);


            $unit->update([
                'status' => 'sold',
            ]);


        } elseif ($validated['payment_status'] === 'partial_payment') {


            $lead->update([
                'current_stage' => 'initial payment',
            ]);


            $unit->update([
                'status' => 'reserved',
            ]);


        } else {


        

            $unit->update([
                'status' => 'reserved',
            ]);

        }

    });


    return redirect()->back()
        ->with('success', 'Deal created successfully!');
}



public function updateDeal(Request $request, $id)
{
    if (Auth::user()->role !== 'admin') {
        return redirect('/');
    }

    $deal = Deal::findOrFail($id);

    $validated = $request->validate([
        'deal_amount' => 'required|numeric|min:0',
        'down_payment' => 'required|numeric|min:0',
        'payment_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
        'payment_status' => 'required|in:pending,fully_paid,partial_payment',
        'number_of_installments' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'commission_type' => 'required|in:percentage,fixed_amount',
        'commission_value' => 'required|numeric|min:0',
        'beneficiary' => 'required|in:internal_agent,external_agent',
        'commission_trigger' => 'required|in:immediate,each_payment,full_payment',
    ]);


    if ($validated['down_payment'] > $validated['deal_amount']) {
        return back()->withErrors([
            'down_payment' => 'Down payment cannot exceed the deal amount.'
        ]);
    }


    
    $remainingAmount = $validated['deal_amount'] - $validated['down_payment'];

    $validated['installment_amount'] = $remainingAmount > 0
        ? $remainingAmount / $validated['number_of_installments']
        : 0;


    DB::transaction(function () use ($deal, $validated) {

       
        $deal->update($validated);


        
        if ($validated['payment_status'] === 'partial_payment') {

            $deal->lead()->update([
                'current_stage' => 'initial payment',
            ]);


            
            $deal->unit()->update([
                'status' => 'reserved',
            ]);


        } elseif ($validated['payment_status'] === 'fully_paid') {

            $deal->lead()->update([
                'current_stage' => 'completed',
            ]);


           
            $deal->unit()->update([
                'status' => 'sold',
            ]);


        } elseif ($validated['payment_status'] === 'pending') {

            

            $deal->unit()->update([
                'status' => 'reserved',
            ]);
        }

    });


    return redirect()->back()
        ->with('success', 'Deal updated successfully!');
}



public function deleteDeal(Request $request, Deal $deal)
{
    abort_if(Auth::user()->role !== 'admin', 403);

    $validated = $request->validate([
        'status' => 'required|in:available,reserved,sold',
        'current_stage' => 'required|in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost',
    ]);

    DB::transaction(function () use ($deal, $validated) {
        if ($deal->unit && $deal->lead) {
            $deal->unit->update([
                'status' => $validated['status'],
            ]);
            $deal->lead->update([
                'current_stage' => $validated['current_stage'],
            ]);
        }

        $deal->delete();
    });

    return back()->with('success', 'Deal deleted and unit and lead status updated successfully.');
}



public function updateDealPaymentStatus(Request $request, Deal $deal)
{
    if (Auth::user()->role !== 'admin') {
        return redirect('/');
    }

    $validated = $request->validate([
        'payment_status' => 'required|in:pending,fully_paid,partial_payment'
    ]);


    DB::transaction(function () use ($deal, $validated) {

    
        $deal->update([
            'payment_status' => $validated['payment_status']
        ]);


       
        if ($validated['payment_status'] === 'partial_payment') {

            $deal->lead()->update([
                'current_stage' => 'initial payment',
            ]);

            $deal->unit()->update([
                'status' => 'reserved',
            ]);


        } elseif ($validated['payment_status'] === 'fully_paid') {

            $deal->lead()->update([
                'current_stage' => 'completed',
            ]);

            $deal->unit()->update([
                'status' => 'sold',
            ]);


        } elseif ($validated['payment_status'] === 'pending') {

            
            $deal->unit()->update([
                'status' => 'reserved',
            ]);
        }

    });


    return redirect()->back()
        ->with('success', 'Payment status updated successfully!');
}

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




