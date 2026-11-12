<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectorControllerGet extends Controller
{
    public function getCollectorDeals(Request $request)
    {
        $this->authorize('viewDealsAsCollector', Deal::class);

        $filters = $request->validate([
            'payment_status' => ['nullable', 'in:pending,partial_payment,fully_paid'],
        ]);

        $deals = Deal::with([
            'lead',
            'project',
            'unit',
        ])
            ->where('collector_id', Auth::id())
            ->when(
                ! empty($filters['payment_status']),
                fn ($query) => $query->where('payment_status', $filters['payment_status'])
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('collector.deals', compact('deals'));
    }
}
