<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;

class UnitControllerGet extends Controller
{
     public function getUnits(){

    $this->authorize('viewAny', Unit::class);
    

    $units = Unit::paginate(2);
    $projects = Project::all();

    return view('admin.units', compact('units', 'projects'));
 }
}
