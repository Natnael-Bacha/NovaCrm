<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\User;

class ActionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Action $action): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Action $action): bool
    {
        return $user->role === 'admin';
    }
}
