<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showCreateUser(){
        return view('user.createUser');
    }



public function createUser(StoreUserRequest $request)
{   
    $this->authorize('create', User::class);
    $validated = $request->validated();

    if ($validated['role'] !== 'agent') {
        $validated['supervisor_id'] = null;
    }

    $validated['password'] = Hash::make($validated['password']);

    User::create($validated);

    return back()->with('success', 'User created successfully.');
}

public function updateRole(UpdateUserRoleRequest $request, User $user)
{
    $this->authorize('update', $user);

    $validated = $request->validated();

    if ($user->role === 'supervisor' && $validated['role'] !== 'supervisor') {

        $agents = $user->agents;

        if ($agents->count() > 0) {
            return redirect()->back()->with([
                'error' => 'This supervisor has assigned agents. Please reassign them first.',
                'supervisor_id' => $user->id,
                'agents' => $agents
            ]);
        }
    }

    if ($validated['role'] !== 'agent') {

        $user->update([
            'role' => $validated['role'],
            'supervisor_id' => null
        ]);

        return redirect()->back()->with('success', 'Role updated successfully');
    }

    $user->update([
        'role' => $validated['role'],
    ]);

    return redirect()->back()->with('success', 'Role updated successfully');
}

 public function updateUser(UpdateUserRequest $request, User $user)
{   
     $this->authorize('update', $user);
    
    $validated = $request->validated();

    if (($validated['role'] ?? null) !== 'agent') {
        $validated['supervisor_id'] = null;
    }

    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return back()->with('success', 'User updated successfully.');
}

public function deleteUser(DeleteUserRequest $request, User $user)
{
   
    
     $this->authorize('delete', $user);
    $isAjax = $request->ajax() || $request->wantsJson();
    
    if ($user->leads()->exists()) {
        if (!$request->new_agent) {
            if ($isAjax) {
                return response()->json([
                    'requires_reassignment' => true
                ]);
            }
            return redirect()->back()->with('warning', 'This user has leads. Please select a new agent to reassign them.');
        }

        $newAgent = User::find($request->new_agent);
        if (!$newAgent) {
            if ($isAjax) {
                return response()->json([
                    'error' => 'Selected agent does not exist'
                ], 422);
            }
            return redirect()->back()->with('error', 'Selected agent does not exist.');
        }

        DB::transaction(function() use ($user, $request) {
            $user->leads()->update(['agent_id' => $request->new_agent]);
            $user->delete();
        });

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted and leads reassigned successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'User deleted and leads reassigned successfully');
    }

    $user->delete();

    if ($isAjax) {
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    
    return redirect()->back()->with('success', 'User deleted successfully');
}
}
