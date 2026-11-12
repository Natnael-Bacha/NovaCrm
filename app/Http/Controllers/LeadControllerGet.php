<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeadControllerGet extends Controller
{
    public function getLeads()
    {

        $this->authorize('viewAny', Lead::class);

        $leads = Lead::all();

        return view('admin.pipeline', compact('leads'));
    }

    public function index()
    {
        $this->authorize('viewAny', Lead::class);

        $deals = Deal::with('lead')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'deals_page');

        $agents = User::agentsUsers()
            ->select('id', 'full_name')
            ->orderBy('full_name', 'desc')
            ->get();

        $collectors = User::where('role', 'collector')
            ->select('id', 'full_name')
            ->orderBy('full_name')
            ->get();

        $leads = Lead::with('agent')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'leads_page');

        $projects = Project::all();
        $units = Unit::all();
      
                    return view('admin.leads', compact(
            'agents',
            'collectors',
            'leads',
            'projects',
            'units',
            'deals'
        ));   
  
    }

public function supervisorLeads()
{
    $this->authorize('viewAny', Lead::class);

    $supervisor = Auth::user();

    $agents = User::agentsUsers()
        ->where('supervisor_id', $supervisor->id)
        ->select('id', 'full_name')
        ->orderBy('full_name')
        ->get();

    $leads = Lead::with('agent')
        ->whereHas('agent', function ($query) use ($supervisor) {
            $query->where('supervisor_id', $supervisor->id);
        })

        // Filter by stage
        ->when(request('stage'), function ($query, $stage) {
            $query->where('current_stage', $stage);
        })

        // Filter by agent
        ->when(request('agent'), function ($query, $agentId) use ($supervisor) {
            $query->where('agent_id', $agentId)
                  ->whereHas('agent', function ($agentQuery) use ($supervisor) {
                      $agentQuery->where('supervisor_id', $supervisor->id);
                  });
        })

        ->orderBy('created_at', 'desc')
        ->paginate(5, ['*'], 'leads_page')
        ->withQueryString();

    return view('supervisor.leads', compact(
        'agents',
        'leads'
    ));
}
}
