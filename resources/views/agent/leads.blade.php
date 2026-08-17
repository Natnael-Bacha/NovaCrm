@extends('layouts.agentSideBar')

@section('title', 'Your Leads · NovaCRM')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">

        <h1 class="text-2xl font-semibold text-gray-800">
            Your Leads
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Leads currently assigned to you.
        </p>

    </div>


    {{-- Leads Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        {{-- Table Header --}}
        <div class="px-6 py-5 border-b border-gray-200">

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Assigned Leads
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    View the customers currently assigned to you.
                </p>
            </div>

        </div>


        {{-- Empty State --}}
        @if($leads->isEmpty())

            <div class="py-16 text-center">

                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">

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
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6z"
                        />
                    </svg>

                </div>

                <h3 class="mt-4 text-sm font-semibold text-gray-800">
                    No leads assigned
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    You currently don't have any leads assigned to you.
                </p>

            </div>

        @else

            {{-- Desktop Table --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Lead
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Contact
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Type
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Location
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Stage
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Source
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($leads as $lead)

                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Lead --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                            style="background-color: #e8eefc; color: #0F286F;"
                                        >
                                            {{ strtoupper(substr($lead->full_name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $lead->full_name }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                Lead #{{ $lead->id }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Contact --}}
                                <td class="px-6 py-4">

                                    <p class="text-sm text-gray-700">
                                        {{ $lead->phone }}
                                    </p>

                                    @if($lead->email)

                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $lead->email }}
                                        </p>

                                    @endif

                                </td>


                                {{-- Type --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ ucfirst($lead->lead_type) }}
                                    </span>

                                </td>


                                {{-- Location --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ $lead->preferred_location ?? '—' }}
                                    </span>

                                </td>


                                {{-- Stage --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ ucfirst($lead->current_stage) }}
                                    </span>

                                </td>


                                {{-- Source --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm text-gray-600">
                                        {{ ucfirst($lead->lead_source) }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-gray-100">

                @foreach($leads as $lead)

                    <div class="p-5">

                        {{-- Lead Header --}}
                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                style="background-color: #e8eefc; color: #0F286F;"
                            >
                                {{ strtoupper(substr($lead->full_name, 0, 1)) }}
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $lead->full_name }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    Lead #{{ $lead->id }}
                                </p>

                            </div>

                        </div>


                        {{-- Lead Details --}}
                        <div class="mt-5 space-y-3">

                            {{-- Phone --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <svg
                                    class="w-4 h-4 text-gray-400 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 011.95.45l1.1 3.32a1 1 0 01-.27 1.02l-2.1 2.1a16 16 0 006.29 6.29l2.1-2.1a1 1 0 011.02-.27l3.32 1.1a1 1 0 01.45 1.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"
                                    />
                                </svg>

                                {{ $lead->phone }}

                            </div>


                            {{-- Email --}}
                            @if($lead->email)

                                <div class="flex items-center gap-2 text-sm text-gray-600">

                                    <svg
                                        class="w-4 h-4 text-gray-400 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>

                                    <span class="truncate">
                                        {{ $lead->email }}
                                    </span>

                                </div>

                            @endif


                            {{-- Location --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <svg
                                    class="w-4 h-4 text-gray-400 flex-shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"
                                    />
                                </svg>

                                {{ $lead->preferred_location ?? 'No location specified' }}

                            </div>


                            {{-- Type --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <span class="text-gray-400">
                                    Type:
                                </span>

                                {{ ucfirst($lead->lead_type) }}

                            </div>


                            {{-- Stage --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <span class="text-gray-400">
                                    Stage:
                                </span>

                                {{ ucfirst($lead->current_stage) }}

                            </div>


                            {{-- Source --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <span class="text-gray-400">
                                    Source:
                                </span>

                                {{ ucfirst($lead->lead_source) }}

                            </div>


                            {{-- Budget --}}
                            @if($lead->budget_range)

                                <div class="flex items-center gap-2 text-sm text-gray-600">

                                    <span class="text-gray-400">
                                        Budget:
                                    </span>

                                    {{ $lead->budget_range }}

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- Pagination --}}
        @if($leads->hasPages())

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leads->links() }}
            </div>

        @endif

    </div>

</div>

@endsection