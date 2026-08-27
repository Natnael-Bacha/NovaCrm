@extends('layouts.supervisorSideBar')

@section('title', 'Lead Management · NovaTra')

@push('scripts')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }

    .brand {
        color: #0F286F;
    }

    .brand-bg {
        background-color: #0F286F;
    }

    /* =========================
       TOAST
    ========================= */

    .toast-container {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-width: 380px;
        width: 100%;
        pointer-events: none;
    }

    .toast {
        pointer-events: auto;
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        background: #ffffff;
        box-shadow:
            0 12px 40px rgba(0, 0, 0, 0.08),
            0 4px 12px rgba(0, 0, 0, 0.04);
        border-left: 6px solid #0F286F;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transform: translateX(120%);
        animation: slideIn 0.35s ease forwards;
        font-size: 0.95rem;
        color: #1e293b;
    }

    .toast-success {
        border-left-color: #22c55e;
    }

    .toast-error {
        border-left-color: #ef4444;
    }

    .toast-icon {
        font-size: 1.3rem;
        line-height: 1;
    }

    .toast-message {
        flex: 1;
        font-weight: 450;
    }

    .toast-close {
        background: none;
        border: none;
        font-size: 1.1rem;
        color: #94a3b8;
        cursor: pointer;
        padding: 0 0.2rem;
    }

    .toast-close:hover {
        color: #475569;
    }

    @keyframes slideIn {
        0% {
            opacity: 0;
            transform: translateX(120%);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .toast-exit {
        animation: slideOut 0.3s ease forwards;
    }

    @keyframes slideOut {
        0% {
            opacity: 1;
            transform: translateX(0);
        }

        100% {
            opacity: 0;
            transform: translateX(120%);
        }
    }

    /* =========================
       FORM / SELECT
    ========================= */

    .form-label {
        display: block;
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0F286F;
    }

    .select-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .select-wrapper select {
        appearance: none;
        -webkit-appearance: none;

        border: 2px solid #0F286F;
        border-radius: 0.75rem;

        padding: 0.75rem 2.5rem 0.75rem 1rem;

        background: white;
        color: #1e293b;

        cursor: pointer;
        transition: all 0.15s ease;

        width: 100%;
        font-size: 0.95rem;
    }

    .select-wrapper select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
    }

    .select-wrapper::after {
        content: '▾';

        position: absolute;
        right: 1rem;
        top: 50%;

        transform: translateY(-50%);

        font-size: 0.7rem;
        color: #0F286F;

        pointer-events: none;
    }

    /* =========================
       STATUS SELECT
    ========================= */

    .status-select-wrapper {
        position: relative;
        display: inline-block;
        vertical-align: middle;
    }

    .status-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;

        padding: 0.5rem 2.2rem 0.5rem 1rem;

        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;

        font-size: 0.8rem;
        font-weight: 500;

        letter-spacing: 0.025em;
        text-transform: uppercase;

        background: white;
        color: #1e293b;

        cursor: pointer;

        transition: all 0.15s ease;

        min-width: 130px;
        height: 36px;

        line-height: 1.2;
    }

    .status-select-wrapper select:hover {
        border-color: #0F286F;
        background: #f8faff;
        box-shadow: 0 2px 8px rgba(15, 40, 111, 0.08);
    }

    .status-select-wrapper select:focus {
        outline: none;
        border-color: #0F286F;
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
    }

    .status-select-wrapper::after {
        content: '▾';

        position: absolute;
        right: 0.8rem;
        top: 50%;

        transform: translateY(-50%);

        font-size: 0.7rem;
        color: #94a3b8;

        pointer-events: none;
    }

    /* =========================
       CARD
    ========================= */

    .card {
        background: white;
        border-radius: 1rem;

        box-shadow:
            0 4px 12px rgba(0, 0, 0, 0.05);

        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-cell-align {
        vertical-align: middle;
    }

    /* =========================
       CLEAR FILTER BUTTON
    ========================= */

    .clear-filters-btn {
        background: white;

        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;

        padding: 0.4rem 1.2rem;

        font-size: 0.75rem;
        font-weight: 500;

        color: #475569;

        transition: all 0.15s ease;

        cursor: pointer;

        display: inline-flex;
        align-items: center;
        gap: 0.4rem;

        white-space: nowrap;
    }

    .clear-filters-btn:hover {
        background: #f8faff;
        border-color: #0F286F;
        color: #0F286F;

        box-shadow:
            0 2px 8px rgba(15, 40, 111, 0.06);
    }

    .clear-filters-btn:active {
        transform: scale(0.96);
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 640px) {

        .toast-container {
            right: 1rem;
            bottom: 1rem;
            left: 1rem;
            max-width: none;
            width: auto;
        }

        .status-select-wrapper select {
            min-width: 115px;
            font-size: 0.7rem;
        }
    }
</style>
@endpush


@section('content')

<!-- =========================================================
     TOAST CONTAINER
========================================================= -->

<div
    class="toast-container"
    x-data="toastManager()"
    x-init="init()"
>
    <template
        x-for="(toast, index) in toasts"
        :key="index"
    >
        <div
            class="toast"
            :class="{
                'toast-success': toast.type === 'success',
                'toast-error': toast.type === 'error'
            }"
        >

            <span
                class="toast-icon"
                x-text="toast.type === 'success' ? '✓' : '✕'"
            ></span>

            <span
                class="toast-message"
                x-text="toast.message"
            ></span>

            <button
                class="toast-close"
                @click="removeToast(index)"
            >
                ✕
            </button>

        </div>
    </template>
</div>


<!-- =========================================================
     MAIN LEADS SECTION
========================================================= -->

<div
    x-data="supervisorLeadManagement()"
    x-init="init()"
    class="p-6 md:p-10"
>

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="flex flex-wrap justify-between items-center mb-8">

        <div>

            <h1 class="text-3xl font-bold brand">
                Lead Management
            </h1>

            <p class="text-gray-500 mt-1 text-sm">
                Manage and monitor leads assigned to your sales team.
            </p>

        </div>

    </div>


    <!-- =====================================================
         FILTER SECTION
    ====================================================== -->

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">

            <!-- Stage Filter -->

            <div>

                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">
                    Filter by Stage
                </label>

                <div class="select-wrapper">

                    <select
                        x-model="selectedStage"
                        @change="applyFilters()"
                    >

                        <option value="">
                            All Stages
                        </option>

                        <option value="new">
                            New
                        </option>

                        <option value="contacted">
                            Contacted
                        </option>

                        <option value="qualified">
                            Qualified
                        </option>

                        <option value="site visit">
                            Site Visit
                        </option>

                        <option value="proposal sent">
                            Proposal Sent
                        </option>

                        <option value="initial payment">
                            Initial Payment
                        </option>

                        <option value="completed">
                            Completed
                        </option>

                        <option value="lost">
                            Lost
                        </option>

                    </select>

                </div>

            </div>


            <!-- Agent Filter -->

            <div>

                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">
                    Filter by Agent
                </label>

                <div class="select-wrapper">

                    <select
                        x-model="filterAgent"
                        @change="applyFilters()"
                    >

                        <option value="">
                            All Agents
                        </option>

                        @foreach($agents as $agent)

                            <option value="{{ $agent->id }}">
                                {{ $agent->full_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <!-- Clear Filters -->

            <div class="flex items-center justify-end md:justify-start">

                <button
                    @click="clearFilters()"
                    class="clear-filters-btn"
                    :class="{
                        'opacity-50 pointer-events-none': !isFiltered()
                    }"
                    :disabled="!isFiltered()"
                >

                    <span>
                        ↺
                    </span>

                    Clear filters

                </button>

            </div>

        </div>

    </div>


    <!-- =====================================================
         LEADS TABLE
    ====================================================== -->

    <div class="card border border-gray-100 shadow-sm">

        <!-- Card Header -->

        <div class="card-header flex flex-wrap justify-between items-center gap-3">

            <div>

                <h2 class="text-xl font-semibold text-slate-800">

                    Team Leads

                    <span class="text-sm font-normal text-gray-400 ml-2">

                        ({{ $leads->total() }} leads)

                    </span>

                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Showing leads assigned to your agents.
                </p>

            </div>

        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50/80">

                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Name
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Phone
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Location
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Stage
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Agent
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($leads as $lead)

                        <tr
                            class="border-b hover:bg-gray-50/60 transition-colors"
                            id="lead-row-{{ $lead->id }}"
                        >

                            <!-- NAME -->

                            <td class="px-6 py-4 font-medium text-slate-700 table-cell-align">

                                {{ $lead->full_name }}

                            </td>


                            <!-- PHONE -->

                            <td class="px-6 py-4 text-gray-600 table-cell-align">

                                {{ $lead->phone }}

                            </td>


                            <!-- LOCATION -->

                            <td class="px-6 py-4 text-gray-600 table-cell-align">

                                {{ $lead->preferred_location }}

                            </td>


                            <!-- STAGE -->

                            <td class="px-6 py-4 table-cell-align">

                                <form
                                    method="POST"
                                    action="{{ route('updateLeadStatus', $lead->id) }}"
                                    class="stage-form"
                                    @submit.prevent
                                >

                                    @csrf

                                    @method('PUT')

                                    <div class="status-select-wrapper">

                                        <select
                                            name="current_stage"
                                            x-data="{ stage: '{{ $lead->current_stage }}' }"
                                            x-model="stage"
                                            @change="updateStage(
                                                {{ $lead->id }},
                                                stage
                                            )"
                                        >

                                            <option value="new">
                                                New
                                            </option>

                                            <option value="contacted">
                                                Contacted
                                            </option>

                                            <option value="qualified">
                                                Qualified
                                            </option>

                                            <option value="site visit">
                                                Site Visit
                                            </option>

                                            <option value="proposal sent">
                                                Proposal Sent
                                            </option>

                                            <option value="initial payment">
                                                Initial Payment
                                            </option>

                                            <option value="completed">
                                                Completed
                                            </option>

                                            <option value="lost">
                                                Lost
                                            </option>

                                        </select>

                                    </div>

                                </form>

                            </td>


                            <!-- AGENT -->

                            <td class="px-6 py-4 table-cell-align">

                                @if($lead->agent)

                                    <span class="text-sm font-medium text-slate-700">

                                        {{ $lead->agent->full_name }}

                                    </span>

                                @else

                                    <span class="text-sm text-gray-400">

                                        Not Assigned

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-12 text-gray-400"
                            >

                                <div class="text-3xl mb-2 text-gray-300">
                                    📭
                                </div>

                                No leads found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- =================================================
             PAGINATION
        ================================================== -->

        @if($leads->hasPages())

            <div class="px-6 py-4 border-t border-gray-100">

                {{ $leads->links('vendor.pagination.custom') }}

            </div>

        @endif

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

    /*
    |--------------------------------------------------------------------------
    | Global CSRF Token
    |--------------------------------------------------------------------------
    */

    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };


    /*
    |--------------------------------------------------------------------------
    | Toast Manager
    |--------------------------------------------------------------------------
    */

    function toastManager() {

        return {

            toasts: [],

            init() {

                window.addToast = (
                    message,
                    type = 'success'
                ) => {

                    this.addToast({
                        message,
                        type
                    });

                };

            },


            addToast({
                message,
                type = 'success'
            }) {

                const now = Date.now();

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate toast messages
                |--------------------------------------------------------------------------
                */

                if (
                    this._lastToast &&
                    this._lastToast.message === message &&
                    this._lastToast.type === type &&
                    (now - this._lastToast.timestamp) < 2000
                ) {

                    return;

                }

                this._lastToast = {
                    message,
                    type,
                    timestamp: now
                };


                this.toasts.push({
                    message,
                    type
                });


                /*
                |--------------------------------------------------------------------------
                | Automatically remove after 5 seconds
                |--------------------------------------------------------------------------
                */

                const index = this.toasts.length - 1;

                setTimeout(() => {

                    this.removeToast(index);

                }, 5000);

            },


            removeToast(index) {

                if (!this.toasts[index]) {
                    return;
                }

                const toastElements =
                    document.querySelectorAll('.toast');

                const toastElement =
                    toastElements[index];

                if (toastElement) {

                    toastElement.classList.add('toast-exit');

                    setTimeout(() => {

                        this.toasts.splice(index, 1);

                    }, 300);

                } else {

                    this.toasts.splice(index, 1);

                }

            }

        };

    }


    /*
    |--------------------------------------------------------------------------
    | Supervisor Lead Management
    |--------------------------------------------------------------------------
    */

    function supervisorLeadManagement() {

        return {

            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            selectedStage: '',

            filterAgent: '',


            /*
            |--------------------------------------------------------------------------
            | Initialization
            |--------------------------------------------------------------------------
            */

            init() {

                this.$nextTick(() => {

                    setTimeout(() => {

                        /*
                        |--------------------------------------------------------------------------
                        | Laravel success message
                        |--------------------------------------------------------------------------
                        */

                        @if(session('success'))

                            if (
                                typeof window.addToast === 'function'
                            ) {

                                window.addToast(
                                    @json(session('success')),
                                    'success'
                                );

                            }

                        @endif


                        /*
                        |--------------------------------------------------------------------------
                        | Laravel error message
                        |--------------------------------------------------------------------------
                        */

                        @if(session('error'))

                            if (
                                typeof window.addToast === 'function'
                            ) {

                                window.addToast(
                                    @json(session('error')),
                                    'error'
                                );

                            }

                        @endif


                        /*
                        |--------------------------------------------------------------------------
                        | Validation errors
                        |--------------------------------------------------------------------------
                        */

                        @if($errors->any())

                            if (
                                typeof window.addToast === 'function'
                            ) {

                                window.addToast(
                                    @json($errors->first()),
                                    'error'
                                );

                            }

                        @endif

                    }, 100);

                });

            },


            /*
            |--------------------------------------------------------------------------
            | Update Lead Stage
            |--------------------------------------------------------------------------
            */

            async updateStage(
                leadId,
                newStage
            ) {

                const url =
                    `{{ url('/updateLeadStatus') }}/${leadId}`;


                try {

                    const response =
                        await fetch(url, {

                            method: 'PUT',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    window.Laravel.csrfToken,

                                'Accept':
                                    'application/json'

                            },

                            body: JSON.stringify({

                                current_stage:
                                    newStage

                            })

                        });


                    /*
                    |--------------------------------------------------------------------------
                    | Try to parse JSON response
                    |--------------------------------------------------------------------------
                    */

                    const data =
                        await response.json();


                    if (
                        response.ok &&
                        data.success
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Show success toast
                        |--------------------------------------------------------------------------
                        */

                        if (
                            typeof window.addToast ===
                            'function'
                        ) {

                            window.addToast(
                                data.message ||
                                'Lead stage updated successfully.',
                                'success'
                            );

                        }

                    } else {

                        throw new Error(
                            data.message ||
                            'Failed to update lead stage.'
                        );

                    }

                } catch (error) {

                    /*
                    |--------------------------------------------------------------------------
                    | Show error toast
                    |--------------------------------------------------------------------------
                    */

                    if (
                        typeof window.addToast ===
                        'function'
                    ) {

                        window.addToast(
                            error.message ||
                            'Something went wrong while updating the lead.',
                            'error'
                        );

                    }

                }

            },


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | These filters currently work on the page displayed by
            | Laravel pagination.
            |
            | When the user changes a filter, this function redirects
            | to the supervisor leads route with query parameters.
            |
            */

            applyFilters() {

                const params =
                    new URLSearchParams();


                if (this.selectedStage) {

                    params.set(
                        'stage',
                        this.selectedStage
                    );

                }


                if (this.filterAgent) {

                    params.set(
                        'agent',
                        this.filterAgent
                    );

                }


                const query =
                    params.toString();


                const baseUrl =
                    '{{ route('supervisor.leads') }}';


                window.location.href =
                    query
                        ? `${baseUrl}?${query}`
                        : baseUrl;

            },


            /*
            |--------------------------------------------------------------------------
            | Clear Filters
            |--------------------------------------------------------------------------
            */

            clearFilters() {

                this.selectedStage = '';
                this.filterAgent = '';


                window.location.href =
                    '{{ route('supervisor.leads') }}';

            },


            /*
            |--------------------------------------------------------------------------
            | Check Whether Filters Are Active
            |--------------------------------------------------------------------------
            */

            isFiltered() {

                return (
                    this.selectedStage !== '' ||
                    this.filterAgent !== ''
                );

            }

        };

    }

</script>

@endsection