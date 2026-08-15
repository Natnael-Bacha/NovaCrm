<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'project_manager',
        'location_address',
        'total_floors',
        'completed_floors',
        'total_units',
        'due_date',
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
