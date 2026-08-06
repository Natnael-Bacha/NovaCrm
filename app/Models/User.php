<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
#[Fillable([
    'full_name',
    'email',
    'password',
    'role',
    'supervisor_id',
    'monthly_target',
])]

#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
])]

class User extends Authenticatable implements PasskeyUser, FilamentUser, HasName
{
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

     public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }


    public function scopeSupervisorsUsers($query)
    {
        return $query->where('role', 'supervisor');
    }


    public function scopeAgentsUsers($query)
    {
        return $query->where('role', 'agent');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'agent_id');
    }

    public function actions()
{
    return $this->hasMany(Action::class, 'assigned_to');
}   
public function getFilamentName(): string
{
    return $this->full_name ?: $this->email;
}
   public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'admin';
}

}