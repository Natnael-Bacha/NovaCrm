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
        $user = auth()->user();
        $actionsQuery = Action::with(['lead', 'assignedUser']);

        if ($user->role === 'supervisor') {
            $users = $user->agents()->where('role', 'agent')->get(['users.id', 'full_name']);
            $agentIds = $users->modelKeys();

            $actionsQuery->whereIn('assigned_to', $agentIds);
            $leads = Lead::whereIn('agent_id', $agentIds)->select('id', 'full_name')->get();
        } else {
            $leads = Lead::select('id', 'full_name')->get();
            $users = User::select('id', 'full_name')->get();
        }

        $actions = $actionsQuery->paginate(5);

        if ($user->role === 'supervisor') {
            return view('supervisor.actions', compact('actions'));
        }

        return view('admin.actions', compact('actions', 'leads', 'users'));
    }
}
