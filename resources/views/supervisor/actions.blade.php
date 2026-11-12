@extends('layouts.supervisorSideBar')

@section('title', 'Team Actions · NovaCRM')

@section('content')
    <div class="p-6 md:p-10">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#0F286F]">Team Actions</h1>
            <p class="mt-1 text-sm text-gray-500">Actions assigned to agents reporting to you.</p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            @if ($actions->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">
                    No actions are currently assigned to your agents.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Lead</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Activity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned Agent</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Scheduled</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($actions as $action)
                                <tr class="border-t border-gray-100">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $action->lead?->full_name ?? 'Lead unavailable' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ str($action->activity_type)->replace('_', ' ')->title() }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $action->assignedUser?->full_name ?? 'Agent unavailable' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        <form method="POST" action="{{ route('updateActionStatus', $action) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <label class="sr-only" for="action-status-{{ $action->id }}">Status for {{ $action->lead?->full_name ?? 'action '.$action->id }}</label>
                                            <select id="action-status-{{ $action->id }}" name="status" onchange="this.form.submit()" class="rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-[#0F286F] focus:outline-none focus:ring-1 focus:ring-[#0F286F]">
                                                <option value="on_progress" @selected($action->status === 'on_progress')>On Progress</option>
                                                <option value="done" @selected($action->status === 'done')>Done</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $action->scheduled_time ? \Illuminate\Support\Carbon::parse($action->scheduled_time)->format('M j, Y g:i A') : 'Not scheduled' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $action->description ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $actions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
