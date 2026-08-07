<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function delete(User $authUser, User $user)
    {
        return $authUser->role === 'admin';
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->role === 'admin';
    }

    public function create(User $authUser)
    {
        return $authUser->role === 'admin';
    }
}