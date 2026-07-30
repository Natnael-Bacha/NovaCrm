<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'lead_id',
        'project_id',
        'unit_id',
        'collector_id',
        'deal_amount',
        'down_payment',
        'payment_status',
        'payment_cycle',
        'number_of_installments',
        'installment_amount',
        'start_date',
        'commission_type',
        'commission_value',
        'beneficiary',
        'commission_trigger',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}