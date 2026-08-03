<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
 
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'budget_range',
        'preferred_location',
        'lead_source',
        'lead_type',
        'current_stage',
        'agent_id',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function deals(){
    return $this->hasMany(Deal::class);
    }

    public function actions()
{
    return $this->hasMany(Action::class);
}
}

