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
    if(Auth::user()->role !== 'admin'){
        return redirect('/');
    }

    $leads = Lead::all();
    

    return view('admin.pipeline', compact('leads'));
 }

  public function index(){
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
}
