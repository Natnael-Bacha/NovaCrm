<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = [
        'project_id',
        'unit_number',
        'floor',
        'unit_type',
        'size',
        'price',
        'status',
    ];

    /**
     * Unit belongs to a project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function deal()
    {
        return $this->hasOne(Deal::class);
    }
}
