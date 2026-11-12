<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentControllerGet extends Controller
{   
    
    public function getAgentLeads()
    {
        $this->authorize('viewLeadsAsAgent', Lead::class);
        $leads = Lead::with('agent')
            ->where('agent_id', Auth::id())
            ->paginate(10);

        return view('agent.leads', compact('leads'));
    }

    public function getAgentDeals()
{
    $this->authorize('viewDealsAsAgent', Deal::class);

    $deals = Deal::with([
        'lead',
        'project',
        'unit',
    ])
    ->whereHas('lead', function ($query) {
        $query->where('agent_id', Auth::id());
    })
    ->latest()
    ->paginate(10);

    return view('agent.deals', compact('deals'));
}



public function getAgentActions(Request $request)
{
    $this->authorize('viewActionsAsAgent', Action::class);

    $filters = $request->validate([
        'date' => ['nullable', 'date'],
        'status' => ['nullable', 'in:done,on_progress'],
        'customer' => ['nullable', 'integer'],
    ]);

    $agentActions = Action::where('assigned_to', Auth::id());
    $customers = Lead::whereIn('id', (clone $agentActions)->distinct()->select('lead_id'))
        ->orderBy('full_name')
        ->get(['id', 'full_name']);

    $actionsQuery = (clone $agentActions)->with('lead');

    if (! empty($filters['date'])) {
        $actionsQuery->whereDate('scheduled_time', $filters['date']);
    }

    if (! empty($filters['status'])) {
        $actionsQuery->where('status', $filters['status']);
    }

    if (! empty($filters['customer'])) {
        $actionsQuery->where('lead_id', $filters['customer']);
    }

    $actions = $actionsQuery
        ->latest('scheduled_time')
        ->paginate(10)
        ->withQueryString();

    return view('agent.actions', compact('actions', 'customers'));
}
}