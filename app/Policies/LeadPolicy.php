<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->role === 'admin';
    }
}