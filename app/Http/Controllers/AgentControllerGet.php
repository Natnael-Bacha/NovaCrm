<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
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
}