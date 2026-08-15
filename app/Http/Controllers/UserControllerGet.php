<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserControllerGet extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $supervisors = User::supervisorsUsers()
            ->select('id', 'full_name')
            ->orderBy('full_name')
            ->get();

        $agents = User::where('role', 'agent')
            ->select('id', 'full_name')
            ->orderBy('full_name')
            ->get();

        $teams = User::orderBy('created_at', 'desc')
            ->paginate(4);

        return view('admin.team', compact(
            'supervisors',
            'teams',
            'agents'
        ));
    }
}
