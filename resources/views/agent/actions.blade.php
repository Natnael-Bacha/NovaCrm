@extends('layouts.agentSideBar')

@section('title', 'Your Actions · NovaCRM')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">

        <h1 class="text-2xl font-semibold text-gray-800">
            Your Actions
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            View and manage the actions assigned to you.
        </p>

    </div>


    {{-- Actions Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        {{-- Table Header --}}
        <div class="px-6 py-5 border-b border-gray-200">

            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    Assigned Actions
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Actions related to your assigned leads.
                </p>
            </div>

        </div>


        {{-- Empty State --}}
        @if($actions->isEmpty())

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
                    No actions found
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    You currently don't have any actions assigned to you.
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
                                Action
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Description
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Scheduled Time
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($actions as $action)

                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Customer --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                            style="background-color: #e8eefc; color: #0F286F;"
                                        >
                                            {{ strtoupper(substr($action->lead->full_name ?? 'N', 0, 1)) }}
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $action->lead->full_name ?? 'Unknown Customer' }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                Lead #{{ $action->lead_id }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Activity Type --}}
                                <td class="px-6 py-4">

                                    <span class="text-sm font-medium text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $action->activity_type)) }}
                                    </span>

                                </td>


                                {{-- Description --}}
                                <td class="px-6 py-4 max-w-xs">

                                    <p class="text-sm text-gray-600 truncate">
                                        {{ $action->description ?: '—' }}
                                    </p>

                                </td>


                                {{-- Scheduled Time --}}
                                <td class="px-6 py-4">

                                    @if($action->scheduled_time)

                                        <div>

                                            <p class="text-sm text-gray-700">
                                                {{ \Carbon\Carbon::parse($action->scheduled_time)->format('M d, Y') }}
                                            </p>

                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($action->scheduled_time)->format('h:i A') }}
                                            </p>

                                        </div>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            Not scheduled
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-4">

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        @if($action->status === 'completed')
                                            bg-green-50 text-green-700
                                        @elseif($action->status === 'pending')
                                            bg-yellow-50 text-yellow-700
                                        @elseif($action->status === 'cancelled')
                                            bg-red-50 text-red-700
                                        @else
                                            bg-gray-100 text-gray-600
                                        @endif"
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $action->status)) }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-gray-100">

                @foreach($actions as $action)

                    <div class="p-5">

                        {{-- Customer --}}
                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                style="background-color: #e8eefc; color: #0F286F;"
                            >
                                {{ strtoupper(substr($action->lead->full_name ?? 'N', 0, 1)) }}
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $action->lead->full_name ?? 'Unknown Customer' }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    Lead #{{ $action->lead_id }}
                                </p>

                            </div>

                        </div>


                        {{-- Action Details --}}
                        <div class="mt-5 space-y-3">

                            {{-- Activity Type --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Action
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $action->activity_type)) }}
                                </span>

                            </div>


                            {{-- Description --}}
                            <div class="flex items-start justify-between gap-4 text-sm">

                                <span class="text-gray-400 flex-shrink-0">
                                    Description
                                </span>

                                <span class="text-gray-600 text-right">
                                    {{ $action->description ?: '—' }}
                                </span>

                            </div>


                            {{-- Scheduled Time --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Scheduled
                                </span>

                                @if($action->scheduled_time)

                                    <span class="text-gray-600">
                                        {{ \Carbon\Carbon::parse($action->scheduled_time)->format('M d, Y h:i A') }}
                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        Not scheduled
                                    </span>

                                @endif

                            </div>


                            {{-- Status --}}
                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-400">
                                    Status
                                </span>

                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($action->status === 'completed')
                                        bg-green-50 text-green-700
                                    @elseif($action->status === 'pending')
                                        bg-yellow-50 text-yellow-700
                                    @elseif($action->status === 'cancelled')
                                        bg-red-50 text-red-700
                                    @else
                                        bg-gray-100 text-gray-600
                                    @endif"
                                >
                                    {{ ucfirst(str_replace('_', ' ', $action->status)) }}
                                </span>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- Pagination --}}
        @if($actions->hasPages())

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $actions->links() }}
            </div>

        @endif

    </div>

</div>

@endsection