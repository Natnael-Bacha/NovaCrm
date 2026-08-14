<?php

namespace App\Http\Controllers;

use App\Models\Deal;

class DealControllerGet extends Controller
{   
public function getDeals()
{
    $this->authorize('viewAny', Deal::class);

    $deals = Deal::with([
        'lead',
        'project',
        'unit',
        'collector'
    ])
    ->orderBy('created_at', 'desc')
    ->paginate(2);
  
    $deals->getCollection()->transform(function ($deal) {
        $deal->start_date = $deal->start_date
            ? $deal->start_date->format('Y-m-d')
            : null;

        return $deal;
    });

    return view('admin.deals', compact('deals'));
}
}
