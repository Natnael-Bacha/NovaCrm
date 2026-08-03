<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminControllerGet extends Controller
{
public function getSupervisors()
{
    
    if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }
  
    $supervisors = User::supervisorsUsers()
        ->select('id', 'full_name')
        ->get();
    
    $agents = User::where('role', 'agent')
        ->select('id', 'full_name')
        ->get();

    $teams = User::all();
    
    

    return view('admin.team', compact('supervisors', 'teams', 'agents'));
}

public function getAgents(){
        if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }
    $deals = Deal::with('lead')->get();
    $agents = User::agentsUsers()
        ->select('id', 'full_name')
        ->orderBy('full_name', 'desc')
        ->get();
    $leads = Lead::with('agent')
        ->orderBy('created_at', 'desc')
        ->get();
    $projects = Project::all();
    $units = Unit::all();
        
    return view('admin.leads', compact('agents', 'leads', 'projects', 'units','deals'));
}


 public function getProjects(){
    if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }

    $projects = Project::all();

    return view('admin.projects', compact('projects'));
 }
 
 

 public function getUnits(){
    if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }

    $units = Unit::all();
    $projects = Project::all();

    return view('admin.units', compact('units', 'projects'));
 }


  public function getLeads(){
    if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }

    $leads = Lead::all();
    

    return view('admin.pipeline', compact('leads'));
 }
 
 public function getDeals(){
    $deals = Deal::with(['lead', 'project', 'unit','collector'])->get()->map(function($deal) {
    $deal->start_date = $deal->start_date ? $deal->start_date->format('Y-m-d') : null;
    return $deal;
});
    
    return view('admin.deals', compact('deals'));
 }

public function getActions()
{
    $actions = Action::with(['lead', 'assignedUser'])->get();
    $leads = Lead::select('id', 'full_name')->get();
    $users = User::select('id', 'full_name')->get();

    return view('admin.actions', compact('actions', 'leads', 'users'));
}

}



