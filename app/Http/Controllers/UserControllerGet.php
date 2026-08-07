<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserControllerGet extends Controller
{
    public function index()
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
}
