@extends('layouts.collectorSideBar')

@section('title', 'Deals to Collect · NovaCRM')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Deals to Collect</h1>
        <p class="mt-1 text-sm text-gray-500">Review payment details for the deals assigned to you.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-800">Your Assigned Deals</h2>
            <p class="mt-1 text-sm text-gray-500">Only deals assigned to your collector account are shown.</p>
        </div>

        <form method="GET" action="{{ route('collector.deals') }}" class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <label for="payment-status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500">Payment Status</label>
            <select
                id="payment-status"
                name="payment_status"
                onchange="this.form.requestSubmit()"
                class="w-full max-w-xs rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-[#0F286F] focus:outline-none focus:ring-1 focus:ring-[#0F286F]"
            >
                <option value="" @selected(! request()->filled('payment_status'))>All statuses</option>
                <option value="pending" @selected(request('payment_status') === 'pending')>Pending</option>
                <option value="partial_payment" @selected(request('payment_status') === 'partial_payment')>Partial payment</option>
                <option value="fully_paid" @selected(request('payment_status') === 'fully_paid')>Fully paid</option>
            </select>
        </form>

        @if ($deals->isEmpty())
            <div class="py-16 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-800">
                    {{ request('payment_status') ? 'No deals match this payment status' : 'No deals assigned' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ request('payment_status') ? 'Choose another status to see more deals.' : 'Deals assigned to you will appear here.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Project / Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Deal Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Down Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Installment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($deals as $deal)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $deal->lead->full_name ?? 'Unknown Customer' }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $deal->lead->phone ?? 'No phone number' }}</p>
                                    <p class="mt-1 text-xs text-gray-400">Deal #{{ $deal->id }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-800">{{ $deal->project->project_name ?? 'Unknown Project' }}</p>
                                    <p class="mt-1 text-xs text-gray-500">Unit {{ $deal->unit->unit_number ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${{ number_format($deal->deal_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($deal->down_payment, 2) }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-800">${{ number_format($deal->installment_amount, 2) }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $deal->number_of_installments }} {{ ucfirst(str_replace('_', ' ', $deal->payment_cycle)) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $paymentStatus = [
                                            'pending' => 'Pending',
                                            'partial_payment' => 'Partial payment',
                                            'fully_paid' => 'Fully paid',
                                        ][$deal->payment_status] ?? ucfirst(str_replace('_', ' ', $deal->payment_status));
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $deal->payment_status === 'fully_paid' ? 'bg-green-100 text-green-700' : ($deal->payment_status === 'partial_payment' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $paymentStatus }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($deals->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $deals->links('vendor.pagination.custom') }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
