@extends('layouts.app')
@section('title', 'Lead Management · NovaTra')
@push('scripts')
 <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
        .brand { color: #0F286F; }
        .brand-bg { background-color: #0F286F; }
        .required::after { content: " *"; color: #ef4444; }

        /* toast */
        .toast-container {
            position: fixed; bottom: 2rem; right: 2rem; z-index: 9999;
            display: flex; flex-direction: column; gap: 0.75rem;
            max-width: 380px; width: 100%; pointer-events: none;
        }
        .toast {
            pointer-events: auto; padding: 1rem 1.5rem; border-radius: 1rem;
            background: #ffffff; box-shadow: 0 12px 40px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
            border-left: 6px solid #0F286F; display: flex; align-items: center; gap: 0.75rem;
            transform: translateX(120%); animation: slideIn 0.35s ease forwards;
            font-size: 0.95rem; color: #1e293b;
        }
        .toast-success { border-left-color: #22c55e; }
        .toast-error { border-left-color: #ef4444; }
        .toast-icon { font-size: 1.3rem; line-height: 1; }
        .toast-message { flex: 1; font-weight: 450; }
        .toast-close { background: none; border: none; font-size: 1.1rem; color: #94a3b8; cursor: pointer; padding: 0 0.2rem; }
        .toast-close:hover { color: #475569; }
        @keyframes slideIn {
            0% { opacity: 0; transform: translateX(120%); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .toast-exit { animation: slideOut 0.3s ease forwards; }
        @keyframes slideOut {
            0% { opacity: 1; transform: translateX(0); }
            100% { opacity: 0; transform: translateX(120%); }
        }

        /* status select */
        .status-select-wrapper {
            position: relative; display: inline-block; vertical-align: middle;
        }
        .status-select-wrapper select {
            appearance: none; -webkit-appearance: none;
            padding: 0.5rem 2.2rem 0.5rem 1rem;
            border: 1.5px solid #e2e8f0; border-radius: 9999px;
            font-size: 0.8rem; font-weight: 500; letter-spacing: 0.025em;
            text-transform: uppercase; background: white; color: #1e293b;
            cursor: pointer; transition: all 0.15s ease; min-width: 130px; height: 36px; line-height: 1.2;
        }
        .status-select-wrapper select:hover {
            border-color: #0F286F; background: #f8faff; box-shadow: 0 2px 8px rgba(15,40,111,0.08);
        }
        .status-select-wrapper select:focus {
            outline: none; border-color: #0F286F; box-shadow: 0 0 0 3px rgba(15,40,111,0.15);
        }
        .status-select-wrapper::after {
            content: '▾'; position: absolute; right: 0.8rem; top: 50%;
            transform: translateY(-50%); font-size: 0.7rem; color: #94a3b8; pointer-events: none;
        }

        .form-input {
            border: 2px solid #0F286F; border-radius: 0.75rem; padding: 0.75rem 1rem;
            width: 100%; transition: all 0.15s ease; background: white;
        }
        .form-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(15,40,111,0.15); }
        .form-label { display: block; margin-bottom: 0.375rem; font-size: 0.875rem; font-weight: 500; color: #0F286F; }
        .btn-primary {
            background-color: #0F286F; color: white; padding: 0.625rem 1.5rem;
            border-radius: 0.75rem; font-weight: 500; transition: opacity 0.15s ease;
            border: none; cursor: pointer;
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary {
            background: white; color: #0F286F; border: 2px solid #0F286F;
            padding: 0.625rem 1.5rem; border-radius: 0.75rem; font-weight: 500;
            transition: background 0.15s ease; cursor: pointer;
        }
        .btn-secondary:hover { background: #f0f4ff; }
        .select-wrapper {
            position: relative; display: inline-block; width: 100%;
        }
        .select-wrapper select {
            appearance: none; -webkit-appearance: none;
            border: 2px solid #0F286F; border-radius: 0.75rem;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            background: white; color: #1e293b; cursor: pointer;
            transition: all 0.15s ease; width: 100%; font-size: 0.95rem;
        }
        .select-wrapper select:focus { outline: none; box-shadow: 0 0 0 3px rgba(15,40,111,0.15); }
        .select-wrapper::after {
            content: '▾'; position: absolute; right: 1rem; top: 50%;
            transform: translateY(-50%); font-size: 0.7rem; color: #0F286F; pointer-events: none;
        }
        .card { background: white; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { padding: 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .card-body { padding: 1.5rem; }
        .table-cell-align { vertical-align: middle; }
        .stage-form { display: inline-block; margin: 0; padding: 0; }

        /* Action buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            background: transparent;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn.edit-btn {
            color: #0F286F;
        }

        .action-btn.edit-btn:hover {
            color: #1a3f8f;
            background: #f0f4ff;
        }

        .action-btn.delete-btn {
            color: #dc2626;
        }

        .action-btn.delete-btn:hover {
            color: #b91c1c;
            background: #fef2f2;
        }

        .action-btn svg {
            width: 18px;
            height: 18px;
        }

        /* Professional Deal Button - Narrower with + icon */
        .btn-deal {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.7rem;
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        .btn-deal:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3);
            background: linear-gradient(135deg, #047857, #065f46);
        }

        .btn-deal:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-deal:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-deal .plus-icon {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1;
            margin-top: -1px;
        }

        .action-group {
            display: flex;
            gap: 0.3rem;
            align-items: center;
            flex-wrap: nowrap;
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            border: 1px solid #f1f4f9;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,40,111,0.02) 0%, transparent 60%);
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -8px rgba(15,40,111,0.08);
            border-color: #dce3f0;
        }
        .stat-card .accent {
            width: 40px;
            height: 4px;
            border-radius: 4px;
            margin-bottom: 0.75rem;
        }
        .stat-card .accent.blue { background: #0F286F; }
        .stat-card .accent.green { background: #059669; }
        .stat-card .accent.amber { background: #d97706; }
        .stat-card .accent.purple { background: #7c3aed; }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0b1b3a;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7a8f;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-top: 0.2rem;
        }
        .stat-desc {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.25rem;
            font-weight: 400;
        }
        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
            .stat-card { padding: 1.25rem; }
            .stat-value { font-size: 1.75rem; }
        }
        @media (max-width: 480px) {
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
            .stat-card { padding: 1rem; }
            .stat-value { font-size: 1.5rem; }
        }

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
            box-shadow: 0 2px 8px rgba(15,40,111,0.06);
        }
        .clear-filters-btn:active {
            transform: scale(0.96);
        }
        .clear-filters-btn i { font-size: 0.7rem; }

        /* Responsive actions column */
        @media (max-width: 640px) {
            .action-group {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            .btn-deal {
                font-size: 0.65rem;
                padding: 0.3rem 0.5rem;
            }
            .btn-deal .plus-icon {
                font-size: 0.85rem;
            }
            .action-btn svg {
                width: 16px;
                height: 16px;
            }
        }

        /* Pending deal row styles - greyish colors, no emoji */
        .row-pending {
            opacity: 0.7;
            background-color: #f3f4f6;
            position: relative;
        }
        .row-pending:hover {
            background-color: #e5e7eb !important;
        }
        .row-pending td:first-child::before {
            content: '';
            display: none;
        }
        .row-pending .status-select-wrapper select,
        .row-pending .action-btn,
        .row-pending .btn-deal {
            pointer-events: none;
            opacity: 0.4;
            cursor: not-allowed;
        }
        .row-pending .status-select-wrapper select {
            background: #e5e7eb;
            border-color: #d1d5db;
        }
        .row-pending .status-select-wrapper::after {
            opacity: 0.2;
        }

        .pending-badge {
            display: inline-block;
            background: #9ca3af;
            color: white;
            padding: 0.3rem 1.2rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.025em;
            text-transform: uppercase;
            border: 1px solid #d1d5db;
        }
    </style>
@endpush

@section('content')

<!-- Toast Container -->
<div class="toast-container" x-data="toastManager()" x-init="init()" @toast.window="addToast($event.detail)">
    <template x-for="(toast, index) in toasts" :key="index">
        <div class="toast" :class="{
            'toast-success': toast.type === 'success',
            'toast-error': toast.type === 'error'
        }" x-init="setTimeout(() => removeToast(index), 5000)">
            <span class="toast-icon" x-text="toast.type === 'success' ? '✓' : '✕'"></span>
            <span class="toast-message" x-text="toast.message"></span>
            <button class="toast-close" @click="removeToast(index)">✕</button>
        </div>
    </template>
</div>

<div 
    x-data="leadManagement()"
    x-init="init()"
    @keydown.escape.window="open=false; editOpen=false; showDeleteModal=false; dealOpen=false"
    class="p-6 md:p-10"
>

    <!-- HEADER -->
    <div class="flex flex-wrap justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold brand">Lead Management</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage prospects, track opportunities, and grow your sales pipeline.</p>
        </div>
        <button @click="open=true" class="brand-bg text-white px-6 py-3 rounded-xl hover:opacity-90 transition flex items-center gap-2 shadow-md shadow-blue-900/10">
            <span>+</span> Add Lead
        </button>
    </div>

    

    <!-- STATS -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value" x-text="totalLeads"></div>
            <div class="stat-label">Total Leads</div>
            <div class="stat-desc mb-1">All-time prospects</div>
            <div class="accent blue"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="newLeads"></div>
            <div class="stat-label">New</div>
            <div class="stat-desc mb-1">Fresh inquiries</div>
            <div class="accent blue"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="activeLeads"></div>
            <div class="stat-label">Active</div>
            <div class="stat-desc mb-1">In progress</div>
            <div class="accent blue"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="completedLeads"></div>
            <div class="stat-label">Completed</div>
            <div class="stat-desc mb-1">Closed / won</div>
            <div class="accent blue"></div>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Filter by Stage</label>
                <div class="select-wrapper">
                    <select x-model="selectedStage">
                        <option value="">All Stages</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="qualified">Qualified</option>
                        <option value="site visit">Site Visit</option>
                        <option value="proposal sent">Proposal Sent</option>
                        <option value="initial payment">Initial Payment</option>
                        <option value="completed">Completed</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Filter by Agent</label>
                <div class="select-wrapper">
                    <select x-model="filterAgent">
                        <option value="">All Agents</option>
                        <template x-for="agent in agents" :key="agent.id">
                            <option :value="agent.id" x-text="agent.full_name"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end md:justify-start gap-3">
                <button 
                    @click="clearFilters()" 
                    class="clear-filters-btn"
                    :class="{ 'opacity-50 pointer-events-none': !isFiltered() }"
                    :disabled="!isFiltered()"
                >
                    <span>↺</span> Clear filters
                </button>
            </div>
        </div>
    </div>

    <!-- LEADS TABLE -->
    <div class="card border border-gray-100 shadow-sm">
        <div class="card-header flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-800">
                All Leads
                <span class="text-sm font-normal text-gray-400 ml-2" x-text="'(' + filteredLeads.length + ' leads)'"></span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stage</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="lead in filteredLeads" :key="lead.id">
                        <tr class="border-b hover:bg-gray-50/60 transition-colors" :class="{ 'row-pending': hasPendingDeal(lead.id) }">
                            <td class="px-6 py-4 font-medium text-slate-700 table-cell-align" x-text="lead.full_name"></td>
                            <td class="px-6 py-4 text-gray-600 table-cell-align" x-text="lead.phone"></td>
                            <td class="px-6 py-4 text-gray-600 table-cell-align" x-text="lead.preferred_location"></td>
                            <td class="px-6 py-4 table-cell-align">
                                <!-- Show pending badge instead of dropdown for pending deals -->
                                <template x-if="hasPendingDeal(lead.id)">
                                    <span class="text-red-500">Pending...</span>
                                </template>
                                <template x-if="!hasPendingDeal(lead.id)">
                                    <form method="POST" :action="'{{ url('/updateLeadStatus') }}/' + lead.id" class="stage-form">
                                        @csrf
                                        @method('PUT')
                                        <div class="status-select-wrapper">
                                            <select name="current_stage" onchange="this.form.submit()" x-model="lead.current_stage">
                                                <option value="new">New</option>
                                                <option value="contacted">Contacted</option>
                                                <option value="qualified">Qualified</option>
                                                <option value="site visit">Site Visit</option>
                                                <option value="proposal sent">Proposal Sent</option>
                                                <option value="initial payment">Initial Payment</option>
                                                <option value="completed">Completed</option>
                                                <option value="lost">Lost</option>
                                            </select>
                                        </div>
                                    </form>
                                </template>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <template x-if="lead.agent">
                                    <span class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-700">
                                        <span x-text="lead.agent.full_name"></span>
                                    </span>
                                </template>
                                <template x-if="!lead.agent">
                                    <span class="text-gray-400 text-sm">Not Assigned</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <div class="action-group">
                                    <!-- Add to Deal Button with + icon - Disabled if lead has deal -->
                                    <button 
                                        type="button"
                                        @click="openDealModal(lead)"
                                        class="btn-deal"
                                        :disabled="hasDeal(lead.id)"
                                        :title="hasDeal(lead.id) ? 'This lead already has a deal' : 'Add to Deal'"
                                    >
                                        <span x-text="hasDeal(lead.id) ? 'Has Deal' : '+ Add to Deal'"></span>
                                    </button>

                                    <!-- Edit Button -->
                                    <button 
                                        type="button"
                                        @click="openEditModal(lead)"
                                        class="action-btn edit-btn"
                                        :class="{ 'opacity-50 pointer-events-none': hasPendingDeal(lead.id) }"
                                        :title="hasPendingDeal(lead.id) ? 'Cannot edit - pending deal' : 'Edit Lead'"
                                        :disabled="hasPendingDeal(lead.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button 
                                        type="button"
                                        @click="
                                            if (hasPendingDeal(lead.id)) {
                                                window.dispatchEvent(new CustomEvent('toast', {
                                                    detail: { message: 'Cannot delete a lead with a pending deal.', type: 'error' }
                                                }));
                                                return;
                                            }
                                            showDeleteModal = true;
                                            deleteLeadId = lead.id;
                                            deleteLeadName = lead.full_name;
                                        "
                                        class="action-btn delete-btn"
                                        :class="{ 'opacity-50 pointer-events-none': hasPendingDeal(lead.id) }"
                                        :title="hasPendingDeal(lead.id) ? 'Cannot delete - pending deal' : 'Delete Lead'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredLeads.length === 0">
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <span class="block text-3xl mb-2 text-gray-300">📭</span>
                            No leads found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="showDeleteModal" x-cloak
    class="fixed inset-0 bg-black/40 flex items-center justify-center p-5 z-50">
        <div @click.outside="showDeleteModal=false"
        class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6">
            
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-red-100">
                <div>
                    <h2 class="text-xl font-bold text-red-600">Delete Lead</h2>
                    <p class="text-sm text-gray-500">Confirm lead deletion</p>
                </div>
                <button type="button" @click="showDeleteModal=false"
                class="text-gray-400 text-xl hover:text-gray-600 transition-colors">✕</button>
            </div>

            <div class="mb-6">
                <div class="flex items-start gap-3 p-4 bg-red-50 rounded-xl border border-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Are you sure you want to delete this lead?</p>
                        <p class="text-sm text-red-700 mt-1">
                            Lead: <span class="font-semibold" x-text="deleteLeadName"></span>
                        </p>
                        <p class="text-xs text-red-600 mt-2">This action cannot be undone. All associated data will be permanently removed.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button 
                    type="button" 
                    @click="showDeleteModal=false"
                    class="px-6 py-2.5 rounded-xl font-medium transition border-2"
                    style="border-color: #0F286F; color: #0F286F; background: white;"
                    @mouseenter="this.style.backgroundColor='#f0f4ff'"
                    @mouseleave="this.style.backgroundColor='white'"
                >
                    Cancel
                </button>
                
                <form method="POST" :action="`{{ url('/deleteLead') }}/${deleteLeadId}`" class="inline">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-6 py-2.5 rounded-xl font-medium transition text-white"
                        style="background-color: #dc2626;"
                        @mouseenter="this.style.opacity='0.9'"
                        @mouseleave="this.style.opacity='1'"
                    >
                        Delete Lead
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT LEAD MODAL -->
    <div x-show="editOpen" x-cloak class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center p-5 z-50">
        <div @click.outside="editOpen=false" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold brand flex items-center gap-2">
                        <span>✎</span> Edit Lead
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Update lead information</p>
                </div>
                <button type="button" @click="editOpen=false" class="text-gray-400 hover:text-gray-600 transition-colors text-xl">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/updateLead') }}/${editLead?.id || ''}`">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label required">Full Name</label>
                        <input 
                            type="text" 
                            name="full_name" 
                            :value="editLead?.full_name || ''" 
                            required 
                            class="form-input" 
                        />
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            :value="editLead?.email || ''" 
                            class="form-input" 
                        />
                    </div>
                    <div>
                        <label class="form-label required">Phone</label>
                        <input 
                            type="text"
                            name="phone" 
                            :value="editLead?.phone || ''" 
                            required 
                            class="form-input" 
                        />
                    </div>
                    <div>
                        <label class="form-label required">Budget Range</label>
                        <div class="select-wrapper">
                            <select name="budget_range" required>
                                <option value="">Select range</option>
                                <option value="500k-1M" :selected="editLead?.budget_range === '500k-1M'">500k - 1M</option>
                                <option value="1M-5M" :selected="editLead?.budget_range === '1M-5M'">1M - 5M</option>
                                <option value="5M+" :selected="editLead?.budget_range === '5M+'">5M+</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Preferred Location</label>
                        <input 
                            type="text"
                            name="preferred_location" 
                            :value="editLead?.preferred_location || ''" 
                            required 
                            class="form-input" 
                        />
                    </div>
                    <div>
                        <label class="form-label required">Lead Source</label>
                        <div class="select-wrapper">
                            <select name="lead_source" required>
                                <option value="">Select source</option>
                                <option value="website" :selected="editLead?.lead_source === 'website'">Website</option>
                                <option value="social media" :selected="editLead?.lead_source === 'social media'">Social Media</option>
                                <option value="referral" :selected="editLead?.lead_source === 'referral'">Referral</option>
                                <option value="walk_in" :selected="editLead?.lead_source === 'walk_in'">Walk In</option>
                                <option value="other" :selected="editLead?.lead_source === 'other'">Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Lead Type</label>
                        <div class="select-wrapper">
                            <select name="lead_type" required>
                                <option value="">Select type</option>
                                <option value="buyer" :selected="editLead?.lead_type === 'buyer'">Buyer</option>
                                <option value="seller" :selected="editLead?.lead_type === 'seller'">Seller</option>
                                <option value="tenant" :selected="editLead?.lead_type === 'tenant'">Tenant</option>
                                <option value="investor" :selected="editLead?.lead_type === 'investor'">Investor</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Current Stage</label>
                        <div class="select-wrapper">
                            <select name="current_stage" required>
                                <option value="">Select stage</option>
                                <option value="new" :selected="editLead?.current_stage === 'new'">New</option>
                                <option value="contacted" :selected="editLead?.current_stage === 'contacted'">Contacted</option>
                                <option value="qualified" :selected="editLead?.current_stage === 'qualified'">Qualified</option>
                                <option value="site visit" :selected="editLead?.current_stage === 'site visit'">Site Visit</option>
                                <option value="proposal sent" :selected="editLead?.current_stage === 'proposal sent'">Proposal Sent</option>
                                <option value="initial payment" :selected="editLead?.current_stage === 'initial payment'">Initial Payment</option>
                                <option value="completed" :selected="editLead?.current_stage === 'completed'">Completed</option>
                                <option value="lost" :selected="editLead?.current_stage === 'lost'">Lost</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Assign Agent</label>
                        <div class="select-wrapper">
                            <select name="agent_id" required>
                                <option value="">Select agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" :selected="editLead?.agent_id == {{ $agent->id }}">
                                        {{ $agent->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" @click="editOpen=false" class="btn-secondary px-5 py-2.5">Cancel</button>
                    <button type="submit" class="btn-primary px-6 py-2.5 flex items-center gap-2">
                        <span>✓</span> Update Lead
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD TO DEAL MODAL -->
    <div x-show="dealOpen" x-cloak class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center p-5 z-50">
        <div @click.outside="dealOpen=false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold text-emerald-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Create Deal
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Lead: <span class="font-medium text-gray-700" x-text="dealLeadName"></span>
                    </p>
                </div>
                <button type="button" @click="dealOpen=false" class="text-gray-400 hover:text-gray-600 transition-colors text-xl">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/createDeal') }}/${dealLeadId}`" @submit="return validateDeal()">
                @csrf

                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Project -->
                    <div>
                        <label class="form-label required">Project</label>
                        <div class="select-wrapper">
                            <select name="project_id" x-model="dealForm.project_id" @change="updateUnits()" required>
                                <option value="">Select project</option>
                                <template x-for="project in projects" :key="project.id">
                                    <option :value="project.id" x-text="project.project_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="form-label required">Unit</label>
                        <div class="select-wrapper">
                            <select name="unit_id" x-model="dealForm.unit_id" required>
                                <option value="">Select unit</option>
                                <template x-for="unit in filteredUnits" :key="unit.id">
                                    <option :value="unit.id" x-text="unit.unit_number + ' - ' + unit.unit_type"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Collector (Required) -->
                    <div>
                        <label class="form-label required">Collector</label>
                        <div class="select-wrapper">
                            <select name="collector_id" x-model="dealForm.collector_id" required>
                                <option value="">Select collector</option>
                                <template x-for="agent in agents" :key="agent.id">
                                    <option :value="agent.id" x-text="agent.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Deal Amount -->
                    <div>
                        <label class="form-label required">Deal Amount</label>
                        <input 
                            type="number" 
                            name="deal_amount" 
                            x-model="dealForm.deal_amount"
                            step="0.01"
                            min="0"
                            required 
                            class="form-input"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Down Payment -->
                    <div>
                        <label class="form-label required">Down Payment</label>
                        <input 
                            type="number" 
                            name="down_payment" 
                            x-model="dealForm.down_payment"
                            step="0.01"
                            min="0"
                            required 
                            class="form-input"
                            placeholder="0.00"
                        />
                        <p class="text-xs text-gray-400 mt-1">Cannot exceed deal amount</p>
                    </div>

                    <!-- Payment Cycle -->
                    <div>
                        <label class="form-label required">Payment Cycle</label>
                        <div class="select-wrapper">
                            <select name="payment_cycle" x-model="dealForm.payment_cycle" required>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="semi_annually">Semi-Annually</option>
                                <option value="annually">Annually</option>
                            </select>
                        </div>
                    </div>

                    <!-- Number of Installments -->
                    <div>
                        <label class="form-label required">Installments</label>
                        <input 
                            type="number" 
                            name="number_of_installments" 
                            x-model="dealForm.number_of_installments"
                            min="1"
                            required 
                            class="form-input"
                        />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="form-label required">Start Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            x-model="dealForm.start_date"
                            required 
                            class="form-input"
                        />
                    </div>

                    <!-- Commission Type -->
                    <div>
                        <label class="form-label required">Commission Type</label>
                        <div class="select-wrapper">
                            <select name="commission_type" x-model="dealForm.commission_type" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                    </div>

                    <!-- Commission Value -->
                    <div>
                        <label class="form-label required">Commission Value</label>
                        <input 
                            type="number" 
                            name="commission_value" 
                            x-model="dealForm.commission_value"
                            step="0.01"
                            min="0"
                            required 
                            class="form-input"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Beneficiary -->
                    <div>
                        <label class="form-label required">Beneficiary</label>
                        <div class="select-wrapper">
                            <select name="beneficiary" x-model="dealForm.beneficiary" required>
                                <option value="internal_agent">Internal Agent</option>
                                <option value="external_agent">External Agent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Commission Trigger -->
                    <div>
                        <label class="form-label required">Commission Trigger</label>
                        <div class="select-wrapper">
                            <select name="commission_trigger" x-model="dealForm.commission_trigger" required>
                                <option value="immediate">Immediate</option>
                                <option value="each_payment">Each Payment</option>
                                <option value="full_payment">Full Payment</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" @click="dealOpen=false" class="btn-secondary px-5 py-2.5">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-medium transition text-white flex items-center gap-2" style="background-color: #059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Create Deal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD LEAD MODAL -->
    <div x-show="open" x-cloak class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center p-5 z-50">
        <div @click.outside="open=false" class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold brand flex items-center gap-2">
                        <span>+</span> Add New Lead
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Enter customer details below</p>
                </div>
                <button type="button" @click="open=false" class="text-gray-400 hover:text-gray-600 transition-colors text-xl">✕</button>
            </div>

            <form method="POST" action="{{ route('createLead') }}">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label required">Full Name</label>
                        <input name="full_name" value="{{ old('full_name') }}" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Budget Range</label>
                        <div class="select-wrapper">
                            <select name="budget_range" required>
                                <option value="">Select range</option>
                                <option value="500k-1M" {{ old('budget_range')=='500k-1M' ? 'selected':'' }}>500k - 1M</option>
                                <option value="1M-5M" {{ old('budget_range')=='1M-5M' ? 'selected':'' }}>1M - 5M</option>
                                <option value="5M+" {{ old('budget_range')=='5M+' ? 'selected':'' }}>5M+</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Preferred Location</label>
                        <input name="preferred_location" value="{{ old('preferred_location') }}" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Lead Source</label>
                        <div class="select-wrapper">
                            <select name="lead_source" required>
                                <option value="">Select source</option>
                                <option value="website" {{ old('lead_source')=='website' ? 'selected':'' }}>Website</option>
                                <option value="social media" {{ old('lead_source')=='social media' ? 'selected':'' }}>Social Media</option>
                                <option value="referral" {{ old('lead_source')=='referral' ? 'selected':'' }}>Referral</option>
                                <option value="walk_in" {{ old('lead_source')=='walk_in' ? 'selected':'' }}>Walk In</option>
                                <option value="other" {{ old('lead_source')=='other' ? 'selected':'' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Lead Type</label>
                        <div class="select-wrapper">
                            <select name="lead_type" required>
                                <option value="">Select type</option>
                                <option value="buyer" {{ old('lead_type')=='buyer' ? 'selected':'' }}>Buyer</option>
                                <option value="seller" {{ old('lead_type')=='seller' ? 'selected':'' }}>Seller</option>
                                <option value="tenant" {{ old('lead_type')=='tenant' ? 'selected':'' }}>Tenant</option>
                                <option value="investor" {{ old('lead_type')=='investor' ? 'selected':'' }}>Investor</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Current Stage</label>
                        <div class="select-wrapper">
                            <select name="current_stage" required>
                                <option value="">Select stage</option>
                                <option value="new" {{ old('current_stage')=='new' ? 'selected':'' }}>New</option>
                                <option value="contacted" {{ old('current_stage')=='contacted' ? 'selected':'' }}>Contacted</option>
                                <option value="qualified" {{ old('current_stage')=='qualified' ? 'selected':'' }}>Qualified</option>
                                <option value="site visit" {{ old('current_stage')=='site visit' ? 'selected':'' }}>Site Visit</option>
                                <option value="proposal sent" {{ old('current_stage')=='proposal sent' ? 'selected':'' }}>Proposal Sent</option>
                                <option value="initial payment" {{ old('current_stage')=='initial payment' ? 'selected':'' }}>Initial Payment</option>
                                <option value="completed" {{ old('current_stage')=='completed' ? 'selected':'' }}>Completed</option>
                                <option value="lost" {{ old('current_stage')=='lost' ? 'selected':'' }}>Lost</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Assign Agent</label>
                        <div class="select-wrapper">
                            <select name="agent_id" required>
                                <option value="">Select agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected':'' }}>
                                        {{ $agent->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" @click="open=false" class="btn-secondary px-5 py-2.5">Cancel</button>
                    <button type="submit" class="btn-primary px-6 py-2.5 flex items-center gap-2">
                        <span>✓</span> Save Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            lastToast: null, // track last toast to avoid duplicates
            init() {},
            addToast({ message, type = 'success' }) {
                // Deduplicate: if the same message and type were added within the last 2 seconds, skip
                const now = Date.now();
                if (this.lastToast && 
                    this.lastToast.message === message && 
                    this.lastToast.type === type && 
                    (now - this.lastToast.timestamp) < 2000) {
                    return;
                }
                this.lastToast = { message, type, timestamp: now };
                this.toasts.push({ message, type });
            },
            removeToast(index) {
                if (this.toasts[index]) {
                    const toastEl = document.querySelectorAll('.toast')[index];
                    if (toastEl) {
                        toastEl.classList.add('toast-exit');
                        setTimeout(() => { this.toasts.splice(index, 1); }, 300);
                    } else {
                        this.toasts.splice(index, 1);
                    }
                }
            }
        };
    }

    function leadManagement() {
        return {
            // State
            open: false,
            editOpen: false,
            editLead: null,
            selectedStage: '',
            filterAgent: '',
            leads: @json($leads ?? []),
            agents: @json($agents ?? []),
            projects: @json($projects ?? []),
            units: @json($units ?? []),
            deals: @json($deals ?? []),
            showDeleteModal: false,
            deleteLeadId: null,
            deleteLeadName: '',
            dealOpen: false,
            dealLeadId: null,
            dealLeadName: '',
            dealForm: {
                project_id: '',
                unit_id: '',
                collector_id: '',
                deal_amount: '',
                down_payment: '',
                payment_cycle: 'monthly',
                number_of_installments: 12,
                start_date: '',
                commission_type: 'percentage',
                commission_value: '',
                beneficiary: 'internal_agent',
                commission_trigger: 'immediate'
            },
            filteredUnits: [],

            // Computed properties
            get filteredLeads() {
                return this.leads.filter(lead => {
                    if (this.selectedStage && lead.current_stage !== this.selectedStage) return false;
                    if (this.filterAgent && lead.agent_id != this.filterAgent) return false;
                    return true;
                });
            },
            get totalLeads() { 
                return this.leads.length; 
            },
            get newLeads() { 
                return this.leads.filter(l => l.current_stage === 'new').length; 
            },
            get completedLeads() { 
                return this.leads.filter(l => l.current_stage === 'completed').length; 
            },
            get activeLeads() { 
                return this.leads.filter(l => ['contacted','qualified','site visit','proposal sent','initial payment'].includes(l.current_stage)).length; 
            },

            // Methods
            init() {
                // Handle any flash messages
                @if(session('success'))
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: '{{ session('success') }}', type: 'success' }
                        }));
                    });
                @endif
                @if(session('error'))
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: '{{ session('error') }}', type: 'error' }
                        }));
                    });
                @endif
                @if($errors->any())
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: '{{ $errors->first() }}', type: 'error' }
                        }));
                    });
                @endif

                // Debug: log initial state
                console.log('Lead management initialized, editOpen:', this.editOpen);
            },

            hasDeal(leadId) {
                return this.deals.some(deal => deal.lead_id === leadId);
            },

            hasPendingDeal(leadId) {
                const deal = this.deals.find(d => d.lead_id === leadId);
                return deal && deal.payment_status === 'pending';
            },

            clearFilters() {
                this.selectedStage = '';
                this.filterAgent = '';
            },

            isFiltered() {
                return this.selectedStage !== '' || this.filterAgent !== '';
            },

            openEditModal(lead) {
                console.log('openEditModal called with lead:', lead);
                
                if (!lead) {
                    console.error('Lead is undefined');
                    return;
                }

                // Prevent editing if pending deal exists
                if (this.hasPendingDeal(lead.id)) {
                    console.log('Lead has pending deal, showing toast');
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { 
                            message: 'Cannot edit a lead with a pending deal.', 
                            type: 'error' 
                        }
                    }));
                    return;
                }

                // Create a deep copy of the lead data to avoid reference issues
                this.editLead = JSON.parse(JSON.stringify(lead));
                console.log('editLead set to:', this.editLead);
                
                // Set editOpen to true
                this.editOpen = true;
                console.log('editOpen set to:', this.editOpen);
            },

            openDealModal(lead) {
                console.log('Opening deal modal for:', lead.full_name);
                
                // Check if lead already has a deal before opening
                if (this.hasDeal(lead.id)) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { 
                            message: 'This lead already has an associated deal.', 
                            type: 'error' 
                        }
                    }));
                    return;
                }

                this.dealLeadId = lead.id;
                this.dealLeadName = lead.full_name;
                this.dealForm = {
                    project_id: '',
                    unit_id: '',
                    collector_id: '',
                    deal_amount: '',
                    down_payment: '',
                    payment_cycle: 'monthly',
                    number_of_installments: 12,
                    start_date: '',
                    commission_type: 'percentage',
                    commission_value: '',
                    beneficiary: 'internal_agent',
                    commission_trigger: 'immediate'
                };
                this.filteredUnits = [];
                this.dealOpen = true;
            },

            updateUnits() {
                if (this.dealForm.project_id) {
                    this.filteredUnits = this.units.filter(unit => 
                        unit.project_id == this.dealForm.project_id && unit.status === 'available'
                    );
                } else {
                    this.filteredUnits = [];
                }
                this.dealForm.unit_id = '';
            },

            validateDeal() {
                const downPayment = parseFloat(this.dealForm.down_payment) || 0;
                const dealAmount = parseFloat(this.dealForm.deal_amount) || 0;
                
                if (downPayment > dealAmount) {
                    alert('Down payment cannot exceed the deal amount.');
                    return false;
                }
                
                if (!this.dealForm.project_id) {
                    alert('Please select a project.');
                    return false;
                }
                
                if (!this.dealForm.unit_id) {
                    alert('Please select a unit.');
                    return false;
                }
                
                if (!this.dealForm.collector_id) {
                    alert('Please select a collector.');
                    return false;
                }
                
                return true;
            }
        };
    }
</script>

@endsection