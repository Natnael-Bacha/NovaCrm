<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteDealRequest;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealPaymentStatusRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class DealController extends Controller
{
    public function createDeal(StoreDealRequest $request, Lead $lead): RedirectResponse
    {

        $this->authorize('create', Deal::class);
        $validated = $request->validated();

        if ($lead->deals()->exists()) {
            return back()->withErrors([
                'lead' => 'This lead already has a deal.',
            ]);
        }

        $validated['lead_id'] = $lead->id;

        if ($validated['down_payment'] > $validated['deal_amount']) {
            return back()->withErrors([
                'down_payment' => 'Down payment cannot exceed deal amount.',
            ])->withInput();
        }

        $unit = Unit::findOrFail($validated['unit_id']);

        if ($unit->status !== 'available') {
            return back()->withErrors([
                'unit_id' => 'This unit is not available.',
            ])->withInput();
        }

        $remainingAmount =
            $validated['deal_amount'] - $validated['down_payment'];

        $validated['installment_amount'] = $remainingAmount > 0
            ? $remainingAmount / $validated['number_of_installments']
            : 0;

        if ($validated['down_payment'] == 0) {

            $validated['payment_status'] = 'pending';

        } elseif ($validated['down_payment'] >= $validated['deal_amount']) {

            $validated['payment_status'] = 'fully_paid';

        } else {

            $validated['payment_status'] = 'partial_payment';
        }

        DB::transaction(function () use ($validated, $lead, $unit) {

            Deal::create($validated);

            if ($validated['payment_status'] === 'fully_paid') {

                $lead->update([
                    'current_stage' => 'completed',
                ]);

                $unit->update([
                    'status' => 'sold',
                ]);

            } elseif ($validated['payment_status'] === 'partial_payment') {

                $lead->update([
                    'current_stage' => 'initial payment',
                ]);

                $unit->update([
                    'status' => 'reserved',
                ]);

            } else {

                $unit->update([
                    'status' => 'reserved',
                ]);
            }
        });

        return redirect()->back()
            ->with('success', 'Deal created successfully!');
    }

    public function updateDeal(UpdateDealRequest $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $validated = $request->validated();

        if ($validated['down_payment'] > $validated['deal_amount']) {
            return back()
                ->withErrors([
                    'down_payment' => 'Down payment cannot exceed the deal amount.',
                ])
                ->withInput();
        }

        $remainingAmount =
            $validated['deal_amount'] - $validated['down_payment'];

        $validated['installment_amount'] = $remainingAmount > 0
            ? $remainingAmount / $validated['number_of_installments']
            : 0;

        DB::transaction(function () use ($deal, $validated) {

            $deal->update($validated);

            if ($validated['payment_status'] === 'partial_payment') {

                $deal->lead()->update([
                    'current_stage' => 'initial payment',
                ]);

                $deal->unit()->update([
                    'status' => 'reserved',
                ]);

            } elseif ($validated['payment_status'] === 'fully_paid') {

                $deal->lead()->update([
                    'current_stage' => 'completed',
                ]);

                $deal->unit()->update([
                    'status' => 'sold',
                ]);

            } elseif ($validated['payment_status'] === 'pending') {

                $deal->unit()->update([
                    'status' => 'reserved',
                ]);
            }
        });

        return redirect()->back()
            ->with('success', 'Deal updated successfully!');
    }

    public function deleteDeal(DeleteDealRequest $request, Deal $deal)
    {
        $this->authorize('delete', $deal);

        $validated = $request->validated();

        DB::transaction(function () use ($deal, $validated) {
            if ($deal->unit && $deal->lead) {
                $deal->unit->update([
                    'status' => $validated['status'],
                ]);
                $deal->lead->update([
                    'current_stage' => $validated['current_stage'],
                ]);
            }

            $deal->delete();
        });

        return back()->with('success', 'Deal deleted and unit and lead status updated successfully.');
    }

    public function updateDealPaymentStatus(UpdateDealPaymentStatusRequest $request, Deal $deal)
    {

        $this->authorize('update', $deal);

        $validated = $request->validated();

        DB::transaction(function () use ($deal, $validated) {

            $deal->update([
                'payment_status' => $validated['payment_status'],
            ]);

            if ($validated['payment_status'] === 'partial_payment') {

                $deal->lead()->update([
                    'current_stage' => 'initial payment',
                ]);

                $deal->unit()->update([
                    'status' => 'reserved',
                ]);

            } elseif ($validated['payment_status'] === 'fully_paid') {

                $deal->lead()->update([
                    'current_stage' => 'completed',
                ]);

                $deal->unit()->update([
                    'status' => 'sold',
                ]);

            } elseif ($validated['payment_status'] === 'pending') {

                $deal->unit()->update([
                    'status' => 'reserved',
                ]);
            }

        });

        return redirect()->back()
            ->with('success', 'Payment status updated successfully!');
    }
}
