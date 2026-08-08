<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Requests\UpdateLeadStatusRequest;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function createLead(StoreLeadRequest $request){
       $this->authorize('create', Lead::class);
      $validated = $request->validated();
      Lead::create($validated);
      return redirect()->back()
        ->with('success','Lead created successfully.');
    }


    public function updateLead(UpdateLeadRequest $request, Lead $lead)
{ 
    $this->authorize('update', $lead);
    $validated = $request->validated();
    $lead->update($validated);


    return redirect()->back()
        ->with('success','Lead updated successfully.');
}


public function updateLeadStatus(UpdateLeadStatusRequest $request,Lead $lead)
{   
    $this->authorize('update', $lead);
    $validated = $request->validated();

    $lead->update([
        'current_stage' => $validated['current_stage']
    ]);

    return redirect()->back()->with('success', 'Lead stage updated successfully');
}

public function deleteLead(Lead $lead)
{
    $this->authorize('delete', $lead);

   

    DB::transaction(function () use ($lead) {

       
        Deal::where('lead_id', $lead->id)->delete();

        $lead->delete();

    });

    return redirect()->back()
        ->with('success', 'Lead deleted successfully.');
}
}
