<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadControllerGet extends Controller
{
    public function getLeads(){
   
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

    $leads = Lead::with('agent')
        ->orderBy('created_at', 'desc')
        ->paginate(5, ['*'], 'leads_page');

    $projects = Project::all();
    $units = Unit::all();

    return view('admin.leads', compact(
        'agents',
        'leads',
        'projects',
        'units',
        'deals'
    ));
}
}
