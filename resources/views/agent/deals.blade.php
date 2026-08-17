@extends('layouts.agentSideBar')

@section('title', 'Your Deals · NovaCRM')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">

        <h1 class="text-2xl font-semibold text-gray-800">
            Your Deals
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Deals associated with your assigned leads.
        </p>

    </div>


    {{-- Deals Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        {{-- Table Header --}}
        <div class="px-6 py-5 border-b border-gray-200">

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Assigned Deals
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    View the deals created from your assigned leads.
                </p>
            </div>

        </div>


        {{-- Empty State --}}
        @if($deals->isEmpty())

            <div class="py-16 text-center">

                <div
                    class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400"
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <h3 class="mt-4 text-sm font-semibold text-gray-800">
                    No deals found
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    You currently don't have any deals associated with your leads.
                </p>

            </div>

        @else

            {{-- Desktop Table --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Customer
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Deal Amount
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Down Payment
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Installment
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Payment Cycle
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Start Date
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($deals as $deal)

                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Customer --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                            style="background-color: #e8eefc; color: #0F286F;"
                                        >
                                            {{ strtoupper(substr($deal->lead->full_name ?? 'N', 0, 1)) }}
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $deal->lead->full_name ?? 'Unknown Customer' }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                Deal #{{ $deal->id }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Deal Amount --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm font-medium text-gray-800">
                                        {{ number_format($deal->deal_amount, 2) }}
                                    </span>

                                </td>


                                {{-- Down Payment --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ number_format($deal->down_payment, 2) }}
                                    </span>

                                </td>


                                {{-- Installment --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ number_format($deal->installment_amount, 2) }}
                                    </span>

                                </td>


                                {{-- Payment Cycle --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $deal->payment_cycle)) }}
                                    </span>

                                </td>


                                {{-- Start Date --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($deal->start_date)->format('M d, Y') }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-gray-100">

                @foreach($deals as $deal)

                    <div class="p-5">

                        {{-- Customer Header --}}
                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                style="background-color: #e8eefc; color: #0F286F;"
                            >
                                {{ strtoupper(substr($deal->lead->full_name ?? 'N', 0, 1)) }}
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $deal->lead->full_name ?? 'Unknown Customer' }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    Deal #{{ $deal->id }}
                                </p>

                            </div>

                        </div>


                        {{-- Deal Details --}}
                        <div class="mt-5 space-y-3">

                            {{-- Deal Amount --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Deal Amount
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ number_format($deal->deal_amount, 2) }}
                                </span>

                            </div>


                            {{-- Down Payment --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Down Payment
                                </span>

                                <span class="text-gray-600">
                                    {{ number_format($deal->down_payment, 2) }}
                                </span>

                            </div>


                            {{-- Installment --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Installment
                                </span>

                                <span class="text-gray-600">
                                    {{ number_format($deal->installment_amount, 2) }}
                                </span>

                            </div>


                            {{-- Number of Installments --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Installments
                                </span>

                                <span class="text-gray-600">
                                    {{ $deal->number_of_installments }}
                                </span>

                            </div>


                            {{-- Payment Cycle --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Payment Cycle
                                </span>

                                <span class="text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $deal->payment_cycle)) }}
                                </span>

                            </div>


                            {{-- Start Date --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Start Date
                                </span>

                                <span class="text-gray-600">
                                    {{ \Carbon\Carbon::parse($deal->start_date)->format('M d, Y') }}
                                </span>

                            </div>


                            {{-- Project --}}
                            @if($deal->project)

                                <div class="flex items-center justify-between text-sm">

                                    <span class="text-gray-400">
                                        Project
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $deal->project->name ?? $deal->project->project_name ?? '—' }}
                                    </span>

                                </div>

                            @endif


                            {{-- Unit --}}
                            @if($deal->unit)

                                <div class="flex items-center justify-between text-sm">

                                    <span class="text-gray-400">
                                        Unit
                                    </span>

                                    <span class="text-gray-600">
                                        {{ $deal->unit->unit_number ?? '—' }}
                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- Pagination --}}
        @if($deals->hasPages())

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $deals->links() }}
            </div>

        @endif

    </div>

</div>

@endsection