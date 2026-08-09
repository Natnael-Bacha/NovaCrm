<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

class ActionControllerGet extends Controller
{
    
public function getActions()
{    
    $this->authorize('viewAny', Action::class);
    $actions = Action::with(['lead', 'assignedUser'])->get();
    $leads = Lead::select('id', 'full_name')->get();
    $users = User::select('id', 'full_name')->get();

    return view('admin.actions', compact('actions', 'leads', 'users'));
}
}
