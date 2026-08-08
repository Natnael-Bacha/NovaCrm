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



