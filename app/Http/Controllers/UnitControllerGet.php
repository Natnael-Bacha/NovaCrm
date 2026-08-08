<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitControllerGet extends Controller
{
     public function getUnits(){

    $this->authorize('viewAny', Unit::class);
    

    $units = Unit::all();
    $projects = Project::all();

    return view('admin.units', compact('units', 'projects'));
 }
}
