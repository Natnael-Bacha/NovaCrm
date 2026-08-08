<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function createUnit(StoreUnitRequest $request)
{
   $this->authorize('create', Unit::class);

   $validated = $request->validated();


    Unit::create($validated);


    return redirect()
        ->back()
        ->with('success', 'Unit created successfully.');
}


public function updateUnit(UpdateUnitRequest $request, Unit $unit)
{
   

    $this->authorize('update', $unit);

    $validated = $request->validated();

    $unit->update($validated);

    return back()->with('success', 'Unit updated successfully.');
}

public function deleteUnit(Unit $unit)
{   
    $this->authorize('delete', $unit);
    
    $unit->delete();
    
    return redirect()->back()->with('success', 'Unit deleted successfully!');
}
}
