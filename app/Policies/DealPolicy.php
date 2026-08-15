<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Deal $deal): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $user->role === 'admin';
    }
}
