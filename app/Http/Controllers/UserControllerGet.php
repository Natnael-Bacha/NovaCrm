<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserControllerGet extends Controller
{
    public function index()
{
    
    $this->authorize('viewAny', User::class);
  
    $supervisors = User::supervisorsUsers()
        ->select('id', 'full_name')
        ->get();
    
    $agents = User::where('role', 'agent')
        ->select('id', 'full_name')
        ->get();

    $teams = User::all();
    
    

    return view('admin.team', compact('supervisors', 'teams', 'agents'));
}
}
