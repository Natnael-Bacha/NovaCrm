@extends('layouts.app')
@section('title', 'Actions Management · NovaTra')
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

    /* Toast */
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
    .toast-success { border-left-color: #0F286F; }
    .toast-error { border-left-color: #0F286F; }
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

    /* General form elements */
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

    /* Modal styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 50;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-overlay[x-show] {
        display: none;
    }
    .modal-overlay[x-show]:not([style*="display: none"]) {
        display: flex !important;
    }

    .modal-content {
        background: white;
        width: 100%;
        max-width: 28rem;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(15, 40, 111, 0.15);
        padding: 1.5rem;
        max-height: 90vh;
        overflow-y: auto;
        border: 2px solid #0F286F;
    }
    .modal-content.wide {
        max-width: 36rem;
    }

    .modal-header {
        border-bottom: 2px solid #0F286F;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-title {
        color: #0F286F;
        font-size: 1.25rem;
        font-weight: 700;
    }
    .modal-subtitle {
        color: #6b7280;
        font-size: 0.875rem;
    }
    .modal-close {
        color: #94a3b8;
        font-size: 1.5rem;
        transition: color 0.15s;
        background: none;
        border: none;
        cursor: pointer;
        line-height: 1;
    }
    .modal-close:hover {
        color: #0F286F;
    }

    .modal-footer {
        border-top: 2px solid #0F286F;
        padding-top: 1rem;
        margin-top: 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Enhanced warning box */
    .warning-box {
        background-color: #fef2f2;
        border: 2px solid #dc2626;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }
    .warning-box-icon {
        color: #dc2626;
        flex-shrink: 0;
    }
    .warning-box-title {
        color: #dc2626;
        font-weight: 700;
        font-size: 0.95rem;
    }
    .warning-box-text {
        color: #1e293b;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .warning-box-text strong {
        color: #0F286F;
    }

    .input-field {
        width: 100%;
        border: 2px solid #0F286F;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.15s;
        outline: none;
        background: white;
    }
    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
    }
    .input-label {
        display: block;
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0F286F;
    }

    .btn-danger {
        background-color: #dc2626;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.15s;
        border: 2px solid #dc2626;
        cursor: pointer;
    }
    .btn-danger:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    }

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
    .action-btn:hover { transform: scale(1.1); }
    .action-btn.edit-btn { color: #0F286F; }
    .action-btn.edit-btn:hover { color: #1a3f8f; background: #f0f4ff; }
    .action-btn.delete-btn { color: #dc2626; }
    .action-btn.delete-btn:hover { color: #b91c1c; background: #fef2f2; }
    .action-btn.view-btn { color: #059669; }
    .action-btn.view-btn:hover { color: #047857; background: #ecfdf5; }
    .action-btn svg { width: 18px; height: 18px; }
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
    .clear-filters-btn:active { transform: scale(0.96); }

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .status-badge.done { background: #d1fae5; color: #065f46; }
    .status-badge.on_progress { background: #fef3c7; color: #92400e; }

    /* Activity type badge */
    .activity-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: capitalize;
        background: #f1f4f9;
        color: #334155;
    }

    /* Inline select styles */
    .inline-select {
        appearance: none;
        -webkit-appearance: none;
        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;
        padding: 0.25rem 1.8rem 0.25rem 0.9rem;
        font-size: 0.75rem;
        font-weight: 500;
        background: white;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.15s ease;
        min-width: 110px;
    }
    .inline-select:hover {
        border-color: #0F286F;
        background: #f8faff;
        box-shadow: 0 2px 8px rgba(15,40,111,0.08);
    }
    .inline-select:focus {
        outline: none;
        border-color: #0F286F;
        box-shadow: 0 0 0 3px rgba(15,40,111,0.15);
    }
    .inline-select-wrapper {
        position: relative;
        display: inline-block;
    }
    .inline-select-wrapper::after {
        content: '▾';
        position: absolute;
        right: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.6rem;
        color: #94a3b8;
        pointer-events: none;
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
    x-data="{
        // Modal flags
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        viewOpen: false,
        
        // Data
        actions: {{ json_encode($actions ?? []) }},
        leads: {{ json_encode($leads ?? []) }},
        users: {{ json_encode($users ?? []) }},
        
        // Filter values
        filterActivityType: '',
        filterStatus: '',
        filterAssignedTo: '',
        
        // Selected items
        selectedAction: null,
        deleteActionId: null,
        deleteLeadName: '',
        deleteActivityType: '',
        
        // Create form
        newAction: {
            lead_id: '',
            activity_type: '',
            assigned_to: '',
            status: 'on_progress',
            scheduled_time: '',
            description: ''
        },
        
        // Edit form
        editForm: {
            id: null,
            lead_id: '',
            activity_type: '',
            assigned_to: '',
            status: '',
            scheduled_time: '',
            description: ''
        },
        
        // Computed
        get filteredActions() {
            return this.actions.filter(action => {
                if (this.filterActivityType && action.activity_type !== this.filterActivityType) return false;
                if (this.filterStatus && action.status !== this.filterStatus) return false;
                if (this.filterAssignedTo && action.assigned_to != this.filterAssignedTo) return false;
                return true;
            });
        },
        get totalActions() { return this.actions.length; },
        get doneActions() { return this.actions.filter(a => a.status === 'done').length; },
        get onProgressActions() { return this.actions.filter(a => a.status === 'on_progress').length; },
        get assignedToMe() {
            const currentUserId = {{ auth()->id() ?? 0 }};
            return this.actions.filter(a => a.assigned_to === currentUserId).length;
        },
        
        // Minimum datetime (today + current time) for the scheduled_time picker
        get minDateTime() {
            return new Date().toISOString().slice(0, 16);
        },
        
        // Methods
        clearFilters() {
            this.filterActivityType = '';
            this.filterStatus = '';
            this.filterAssignedTo = '';
        },
        isFiltered() {
            return this.filterActivityType !== '' || this.filterStatus !== '' || this.filterAssignedTo !== '';
        },
        openCreateModal() {
            this.newAction = {
                lead_id: '',
                activity_type: '',
                assigned_to: '',
                status: 'on_progress',
                scheduled_time: '',
                description: ''
            };
            this.createOpen = true;
        },
        openEditModal(action) {
            this.editForm = {
                id: action.id,
                lead_id: action.lead_id,
                activity_type: action.activity_type,
                assigned_to: action.assigned_to,
                status: action.status,
                scheduled_time: action.scheduled_time ? new Date(action.scheduled_time).toISOString().slice(0, 16) : '',
                description: action.description || ''
            };
            this.editOpen = true;
        },
        openDeleteModal(action) {
            this.deleteActionId = action.id;
            this.deleteLeadName = action.lead ? action.lead.full_name : 'N/A';
            this.deleteActivityType = action.activity_type;
            this.deleteOpen = true;
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        getStatusBadge(status) {
            return status === 'done' ? 'status-badge done' : 'status-badge on_progress';
        },
        getStatusLabel(status) {
            return status === 'done' ? 'Done' : 'On Progress';
        },
        getActivityLabel(type) {
            const labels = {
                'follow_up_call': 'Follow-up Call',
                'meeting': 'Meeting',
                'property_visit': 'Property Visit',
                'email': 'Email'
            };
            return labels[type] || type;
        }
    }"
    x-init="
        @if(session('success'))
            $nextTick(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: '{{ session('success') }}', type: 'success' }
                }));
            });
        @endif
        @if(session('error'))
            $nextTick(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: '{{ session('error') }}', type: 'error' }
                }));
            });
        @endif
        @if($errors->any())
            $nextTick(() => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: '{{ $errors->first() }}', type: 'error' }
                }));
            });
        @endif
    "
    @keydown.escape.window="createOpen=false; editOpen=false; deleteOpen=false; viewOpen=false"
    class="p-6 md:p-10"
>

    <!-- HEADER -->
    <div class="flex flex-wrap justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold brand">Actions Management</h1>
            <p class="text-gray-500 mt-1 text-sm">Track and manage all lead actions across your team.</p>
        </div>
        <div>
            <button @click="openCreateModal()" class="btn-primary flex items-center gap-2 px-5 py-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Action
            </button>
        </div>
    </div>

    <!-- STATS -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value" x-text="totalActions"></div>
            <div class="stat-label">Total Actions</div>
            <div class="stat-desc mb-1">All activities</div>
            <div class="accent blue"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="doneActions"></div>
            <div class="stat-label">Done</div>
            <div class="stat-desc mb-1">Completed actions</div>
            <div class="accent green"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="onProgressActions"></div>
            <div class="stat-label">On Progress</div>
            <div class="stat-desc mb-1">Pending actions</div>
            <div class="accent amber"></div>
        </div>
        <div class="stat-card">
            <div class="stat-value" x-text="assignedToMe"></div>
            <div class="stat-label">Assigned to Me</div>
            <div class="stat-desc mb-1">Your tasks</div>
            <div class="accent purple"></div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Activity Type</label>
                <div class="select-wrapper">
                    <select x-model="filterActivityType">
                        <option value="">All Types</option>
                        <option value="follow_up_call">Follow-up Call</option>
                        <option value="meeting">Meeting</option>
                        <option value="property_visit">Property Visit</option>
                        <option value="email">Email</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Status</label>
                <div class="select-wrapper">
                    <select x-model="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="done">Done</option>
                        <option value="on_progress">On Progress</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Assigned To</label>
                <div class="select-wrapper">
                    <select x-model="filterAssignedTo">
                        <option value="">All Users</option>
                        <template x-for="user in users" :key="user.id">
                            <option :value="user.id" x-text="user.full_name"></option>
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

    <!-- TABLE -->
    <div class="card border border-gray-100 shadow-sm">
        <div class="card-header flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-800">
                All Actions
                <span class="text-sm font-normal text-gray-400 ml-2" x-text="'(' + filteredActions.length + ' actions)'"></span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lead</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Scheduled</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="action in filteredActions" :key="action.id">
                        <tr class="border-b hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-700" x-text="'#' + action.id"></td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-700" x-text="action.lead ? action.lead.full_name : 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <!-- INLINE UPDATE FOR ACTIVITY TYPE -->
                                <form method="POST" :action="`{{ url('/updateActionActivity') }}/${action.id}`" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <div class="inline-select-wrapper">
                                        <select name="activity_type" onchange="this.form.submit()" class="inline-select">
                                            <option value="follow_up_call" :selected="action.activity_type === 'follow_up_call'">Follow-up Call</option>
                                            <option value="meeting" :selected="action.activity_type === 'meeting'">Meeting</option>
                                            <option value="property_visit" :selected="action.activity_type === 'property_visit'">Property Visit</option>
                                            <option value="email" :selected="action.activity_type === 'email'">Email</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700" x-text="action.assigned_user ? action.assigned_user.full_name : 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <!-- INLINE UPDATE FOR STATUS -->
                                <form method="POST" :action="`{{ url('/updateActionStatus') }}/${action.id}`" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <div class="inline-select-wrapper">
                                        <select name="status" onchange="this.form.submit()" class="inline-select">
                                            <option value="on_progress" :selected="action.status === 'on_progress'">On Progress</option>
                                            <option value="done" :selected="action.status === 'done'">Done</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-gray-600" x-text="formatDate(action.scheduled_time)"></td>
                            <td class="px-6 py-4">
                                <div class="action-group">
                                    <!-- Edit -->
                                    <button @click="openEditModal(action)" class="action-btn edit-btn" title="Edit Action">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <!-- Delete -->
                                    <button @click="openDeleteModal(action)" class="action-btn delete-btn" title="Delete Action">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredActions.length === 0">
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <span class="block text-3xl mb-2 text-gray-300">📭</span>
                            No actions found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- CREATE ACTION MODAL                           -->
    <!-- ============================================= -->
    <div x-show="createOpen" x-cloak style="display: none;" class="modal-overlay" @click.outside="createOpen=false">
        <div class="modal-content wide">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">➕ Create Action</h2>
                    <p class="modal-subtitle">Add a new action for a lead</p>
                </div>
                <button type="button" @click="createOpen=false" class="modal-close">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/createAction') }}/${newAction.lead_id}`" @submit.prevent="
                if (!newAction.lead_id) {
                    alert('Please select a lead.');
                    return;
                }
                $el.submit();
            ">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Lead -->
                    <div>
                        <label class="input-label required">Lead</label>
                        <div class="select-wrapper">
                            <select name="lead_id" x-model="newAction.lead_id" required>
                                <option value="">Select Lead</option>
                                <template x-for="lead in leads" :key="lead.id">
                                    <option :value="lead.id" x-text="lead.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <label class="input-label required">Activity Type</label>
                        <div class="select-wrapper">
                            <select name="activity_type" x-model="newAction.activity_type" required>
                                <option value="">Select Type</option>
                                <option value="follow_up_call">Follow-up Call</option>
                                <option value="meeting">Meeting</option>
                                <option value="property_visit">Property Visit</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="input-label required">Assigned To</label>
                        <div class="select-wrapper">
                            <select name="assigned_to" x-model="newAction.assigned_to" required>
                                <option value="">Select User</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="input-label required">Status</label>
                        <div class="select-wrapper">
                            <select name="status" x-model="newAction.status" required>
                                <option value="on_progress">On Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scheduled Time – min attribute prevents past dates -->
                    <div>
                        <label class="input-label required">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_time" x-model="newAction.scheduled_time" 
                               :min="minDateTime" required class="form-input" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="input-label">Description</label>
                        <textarea name="description" x-model="newAction.description" rows="2" class="form-input" placeholder="Optional notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="createOpen=false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Create Action</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- EDIT ACTION MODAL                             -->
    <!-- ============================================= -->
    <div x-show="editOpen" x-cloak style="display: none;" class="modal-overlay" @click.outside="editOpen=false">
        <div class="modal-content wide">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">✎ Edit Action</h2>
                    <p class="modal-subtitle">Update action details</p>
                </div>
                <button type="button" @click="editOpen=false" class="modal-close">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/updateAction') }}/${editForm.id}`">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Lead -->
                    <div>
                        <label class="input-label required">Lead</label>
                        <div class="select-wrapper">
                            <select name="lead_id" x-model="editForm.lead_id" required>
                                <option value="">Select Lead</option>
                                <template x-for="lead in leads" :key="lead.id">
                                    <option :value="lead.id" x-text="lead.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <label class="input-label required">Activity Type</label>
                        <div class="select-wrapper">
                            <select name="activity_type" x-model="editForm.activity_type" required>
                                <option value="follow_up_call">Follow-up Call</option>
                                <option value="meeting">Meeting</option>
                                <option value="property_visit">Property Visit</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="input-label required">Assigned To</label>
                        <div class="select-wrapper">
                            <select name="assigned_to" x-model="editForm.assigned_to" required>
                                <option value="">Select User</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="input-label required">Status</label>
                        <div class="select-wrapper">
                            <select name="status" x-model="editForm.status" required>
                                <option value="on_progress">On Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scheduled Time – min attribute prevents past dates -->
                    <div>
                        <label class="input-label required">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_time" x-model="editForm.scheduled_time" 
                               :min="minDateTime" required class="form-input" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="input-label">Description</label>
                        <textarea name="description" x-model="editForm.description" rows="2" class="form-input" placeholder="Optional notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="editOpen=false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Update Action</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- DELETE ACTION MODAL                           -->
    <!-- ============================================= -->
    <div x-show="deleteOpen" x-cloak style="display: none;" class="modal-overlay" @click.outside="deleteOpen=false">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">⚠️ Delete Action</h2>
                    <p class="modal-subtitle">This action will be permanently removed</p>
                </div>
                <button type="button" @click="deleteOpen=false" class="modal-close">✕</button>
            </div>

            <div>
                <div class="warning-box">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="warning-box-icon h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="warning-box-title">You are about to delete this action!</p>
                            <p class="warning-box-text">
                                <strong>Lead:</strong> <span x-text="deleteLeadName"></span><br>
                                <strong>Activity:</strong> <span x-text="getActivityLabel(deleteActivityType)"></span>
                            </p>
                            <p class="text-xs text-red-600 mt-1 font-semibold">This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" @click="deleteOpen=false" class="btn-secondary">Cancel</button>
                <form method="POST" :action="`{{ url('/actions') }}/${deleteActionId}`">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Delete Action</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            init() {},
            addToast({ message, type = 'success' }) {
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
</script>

@endsection