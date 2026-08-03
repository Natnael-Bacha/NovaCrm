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
    public function createLead(Request $request){
    $request->merge([
    'full_name' => strip_tags($request->full_name),
    'budget_range' => strip_tags($request->budget_range),
    'preferred_location' => strip_tags($request->preferred_location),
]);

 $validated = $request->validate([
    'full_name'=>'required|string|max:255',
    'email'=>'nullable|email',
    'phone'=>'required|string|max:20',
    'budget_range'=>'nullable|string|max:100',
    'preferred_location'=>'nullable|string|max:255',
    'lead_source'=>'required|in:website,social media,referral,walk_in,other',
    'lead_type'=>'required|in:buyer,seller,tenant,investor',
    'current_stage'=>'required|in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost',
    'agent_id'=>'nullable|exists:users,id'
]);
      Lead::create($validated);
      return redirect()->route('leads');
    }



public function createUser(Request $request)
{
   
    abort_if(Auth::user()->role !== 'admin', 403);

   
    $request->merge(
        collect($request->all())
            ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
            ->toArray()
    );

    $validated = $request->validate([
        'full_name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'lowercase',
            'max:255',
            'unique:users,email',
        ],

        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
        ],

        'role' => [
            'required',
            'in:admin,supervisor,agent,collector',
        ],

        'supervisor_id' => [
            'nullable',
            'exists:users,id',
        ],

        'monthly_target' => [
            'required',
            'integer',
            'min:0',
        ],
    ]);

    if ($validated['role'] !== 'agent') {
        $validated['supervisor_id'] = null;
    }

  
    if (
        $validated['role'] === 'agent' &&
        empty($validated['supervisor_id'])
    ) {
        return back()
            ->withErrors([
                'supervisor_id' => 'Please assign a supervisor.'
            ])
            ->withInput();
    }

    $validated['password'] = Hash::make($validated['password']);

    User::create($validated);

    return back()->with('success', 'User created successfully.');
}



public function updateRole(Request $request, $id)
{
    $validated = $request->validate([
        'role' => 'required|in:admin,supervisor,agent,collector'
    ]);

    $user = User::findOrFail($id);

  
    if($user->role === 'supervisor' && $validated['role'] !== 'supervisor') {

        $agents = $user->agents;

        if($agents->count() > 0){

            return redirect()->back()->with([
                'error' => 'This supervisor has assigned agents. Please reassign them first.',
                'supervisor_id' => $user->id,
                'agents' => $agents
            ]);
        }
    }
    //    if ($validated['role'] !== 'agent') {
    //     $validated['supervisor_id'] = null;
    // }

    $user->update([
        'role' => $validated['role']
    ]);


    return redirect()->back()->with('success','Role updated successfully');
}

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
        ->route('getSupervisors')
        ->with('success', 'Agents reassigned and role updated.');
}

public function updateLeadStatus(Request $request, $id)
{
    $validated = $request->validate([
        'current_stage' => 'required|in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost'
    ]);

    $lead = Lead::findOrFail($id);

    $lead->update([
        'current_stage' => $validated['current_stage']
    ]);

    return redirect()->back()->with('success', 'Lead stage updated successfully');
}

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


 

 public function updateUser(Request $request, $id)
{
    abort_if(Auth::user()->role !== 'admin', 403);

    $user = User::findOrFail($id);

    $request->merge(
        collect($request->all())
            ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
            ->toArray()
    );

    $validated = $request->validate([
        'full_name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'lowercase',
            'max:255',
            Rule::unique('users')->ignore($user->id),
        ],

        'role' => [
            'nullable',
            'in:admin,supervisor,agent,collector',
        ],

        'supervisor_id' => [
            'nullable',
            'exists:users,id',
        ],

        'monthly_target' => [
            'required',
            'integer',
            'min:0',
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
        ],
    ]);

     if(!empty($validated['role'])){
            if ($validated['role'] !== 'agent' ) {
        $validated['supervisor_id'] = null;
    }

    
    if (
        $validated['role'] === 'agent' &&
        empty($validated['supervisor_id'])
    ) {
        return back()
            ->withErrors([
                'supervisor_id' => 'Please assign a supervisor.'
            ])
            ->withInput();
    }
     }

    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }


    $user->update($validated);

    return redirect()->back()
        ->with('success', 'User updated successfully.');
}

public function deleteUser(Request $request, $id)
{
    $user = User::findOrFail($id);
    
  
    $isAjax = $request->ajax() || $request->wantsJson();
    
    if ($user->leads()->exists()) {
        if (!$request->new_agent) {
            if ($isAjax) {
                return response()->json([
                    'requires_reassignment' => true
                ]);
            }
            return redirect()->back()->with('warning', 'This user has leads. Please select a new agent to reassign them.');
        }

        $newAgent = User::find($request->new_agent);
        if (!$newAgent) {
            if ($isAjax) {
                return response()->json([
                    'error' => 'Selected agent does not exist'
                ], 422);
            }
            return redirect()->back()->with('error', 'Selected agent does not exist.');
        }

        DB::transaction(function() use ($user, $request) {
            $user->leads()->update(['agent_id' => $request->new_agent]);
            $user->delete();
        });

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted and leads reassigned successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'User deleted and leads reassigned successfully');
    }

    $user->delete();

    if ($isAjax) {
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    
    return redirect()->back()->with('success', 'User deleted successfully');
}


public function updateLead(Request $request, $id)
{
    abort_if(Auth::user()->role !== 'admin', 403);

    $lead = Lead::findOrFail($id);

    $validated = $request->validate([

        'full_name' => [
            'required',
            'string',
            'max:255'
        ],

        'email' => [
            'nullable',
            'email',
            'max:255',
            'lowercase'
        ],

        'phone' => [
            'required',
            'string',
            'max:20'
        ],

        'budget_range' => [
            'nullable',
            'string',
            'max:100'
        ],

        'preferred_location' => [
            'nullable',
            'string',
            'max:255'
        ],

        'lead_source' => [
            'required',
            'in:website,social media,referral,walk_in,other'
        ],

        'lead_type' => [
            'required',
            'in:buyer,seller,tenant,investor'
        ],

        'current_stage' => [
            'required',
            'in:new,contacted,qualified,site visit,proposal sent,initial payment,completed,lost'
        ],

        'agent_id' => [
            'nullable',
            Rule::exists('users','id')
                ->where('role','agent')
        ]
    ]);


    $lead->update($validated);


    return redirect()->back()
        ->with('success','Lead updated successfully.');
}



public function deleteLead($id)
{
    abort_if(Auth::user()->role !== 'admin', 403);

    $lead = Lead::findOrFail($id);

    DB::transaction(function () use ($lead) {

       
        Deal::where('lead_id', $lead->id)->delete();

        $lead->delete();

    });

    return redirect()->back()
        ->with('success', 'Lead deleted successfully.');
}

public function updateProject(Request $request, $id)
{
    $request->merge(
        collect($request->all())
            ->map(fn ($value) => is_string($value) ? strip_tags(trim($value)) : $value)
            ->toArray()
    );
    
    abort_if(Auth::user()->role !== 'admin', 403);
    $project = Project::findOrFail($id);
    
    $rules = [
        'project_manager' => ['required', 'string', 'min:3'],
        'location_address' => ['required', 'string', 'min:3'],
        'total_floors' => ['required', 'integer', 'min:1'],
        'completed_floors' => ['required', 'integer', 'min:0'],
        'total_units' => ['required', 'integer', 'min:1'],
        'due_date' => ['required', 'date'],
    ];

    if ($request->project_name !== $project->project_name) {
        $rules['project_name'] = [
            'required',
            'string',
            'min:3',
            Rule::unique('projects', 'project_name')->ignore($project->id),
        ];
    } else {
        $rules['project_name'] = ['required', 'string', 'min:3'];
    }
    
    $validated = $request->validate($rules);
    
    if ($validated['completed_floors'] > $validated['total_floors']) {
        return redirect()->back()
            ->withErrors([
                'completed_floors' => 'Completed floors cannot exceed total floors.'
            ])
            ->withInput()
            ->with('edit_error', true)
            ->with('edit_project_id', $id);
    }
    
    $project->update($validated);
    
    return redirect()->back()
        ->with('success', 'Project updated successfully.');
}
public function getProjectData($id)
{
    $project = Project::find($id);
    
    
    if (!$project) {
        return response()->json(['error' => 'Project not found'], 404);
    }
    
    return response()->json($project);
}



public function deleteProject($id)
{
     abort_if(Auth::user()->role !== 'admin', 403);
    $project = Project::findorFail($id);
    
    if (!$project) {
        return redirect()->back()->with('error', 'Project not found');
    }
    
    $project->delete();
    
    return redirect()->back()->with('success', 'Project deleted successfully!');
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


public function updateLeadStage(Request $request, $id)
{
    try {
        $lead = Lead::findOrFail($id);
        $lead->current_stage = $request->stage;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead stage updated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update lead stage: ' . $e->getMessage()
        ], 500);
    }
}


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




