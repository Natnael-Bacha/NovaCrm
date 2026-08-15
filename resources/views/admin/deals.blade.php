@extends('layouts.app')
@section('title', 'Deal Management · NovaTra')
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

    /* Modal styles (from Manage Teams) */
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

    /* Enhanced warning box – red/amber */
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

    /* Payment Status Select (keep existing) */
    .payment-status-wrapper {
        position: relative;
        display: inline-block;
    }
    .payment-status-wrapper select {
        appearance: none; -webkit-appearance: none;
        padding: 0.3rem 2rem 0.3rem 0.9rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        letter-spacing: 0.025em;
        text-transform: uppercase;
        background: white;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.15s ease;
        min-width: 110px;
    }
    .payment-status-wrapper select:hover {
        border-color: #0F286F;
        background: #f0f4ff;
        box-shadow: 0 2px 8px rgba(15, 40, 111, 0.08);
    }
    .payment-status-wrapper select:focus {
        outline: none;
        border-color: #0F286F;
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
    }
    .payment-status-wrapper::after {
        content: '▾';
        position: absolute;
        right: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.6rem;
        color: #94a3b8;
        pointer-events: none;
    }
    .payment-status-wrapper select option[value="fully_paid"] { color: #065f46; }
    .payment-status-wrapper select option[value="partial_payment"] { color: #92400e; }
    .payment-status-wrapper select option[value="pending"] { color: #991b1b; }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .payment-badge.fully_paid { background: #d1fae5; color: #065f46; }
    .payment-badge.partial_payment { background: #fef3c7; color: #92400e; }
    .payment-badge.pending { background: #fee2e2; color: #991b1b; }

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

    /* Responsive */
    @media (max-width: 640px) {
        .action-group { flex-wrap: wrap; gap: 0.25rem; }
        .action-btn svg { width: 16px; height: 16px; }
        .payment-status-wrapper select { min-width: 90px; font-size: 0.6rem; padding: 0.25rem 1.8rem 0.25rem 0.7rem; }
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
        editOpen: false,
        editDeal: {},
        viewOpen: false,
        viewDeal: null,
        filterPaymentStatus: '',
        filterProject: '',
        deals: {{ json_encode($deals->items()) }},
        projects: {{ json_encode($projects ?? []) }},
        showDeleteModal: false,
        deleteDealId: null,
        deleteDealReference: '',
        deleteLeadName: '',
        deleteUnitNumber: '',
        selectedUnitStatus: '',
        selectedLeadStage: '',

        get filteredDeals() {
            return this.deals.filter(deal => {
                if (this.filterPaymentStatus && deal.payment_status !== this.filterPaymentStatus) return false;
                if (this.filterProject && deal.project_id != this.filterProject) return false;
                return true;
            });
        },
        clearFilters() {
            this.filterPaymentStatus = '';
            this.filterProject = '';
        },
        isFiltered() {
            return this.filterPaymentStatus !== '' || this.filterProject !== '';
        },
        openEditModal(deal) {
            // clone so we don't mutate the row in the table until the form actually submits
            this.editDeal = JSON.parse(JSON.stringify(deal));
            if (this.editDeal.start_date) {
                this.editDeal.start_date = new Date(this.editDeal.start_date).toISOString().split('T')[0];
            }
            this.editOpen = true;
        },
        openViewModal(deal) {
            this.viewDeal = deal;
            this.viewOpen = true;
        },
        formatCurrency(amount) {
            if (!amount) return '0.00';
            return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        getPaymentBadge(status) {
            const classes = {
                'fully_paid': 'payment-badge fully_paid',
                'partial_payment': 'payment-badge partial_payment',
                'pending': 'payment-badge pending'
            };
            return classes[status] || 'payment-badge pending';
        },
        getPaymentLabel(status) {
            const labels = {
                'fully_paid': 'Fully Paid',
                'partial_payment': 'Partial Payment',
                'pending': 'Pending'
            };
            return labels[status] || 'Pending';
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
        showDeleteModal = false;
    "
    @keydown.escape.window="editOpen=false; viewOpen=false; showDeleteModal=false"
    class="p-6 md:p-10"
>

    <!-- HEADER -->
    <div class="flex flex-wrap justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold brand">Deal Management</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage all deals, track progress, and monitor sales performance.</p>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Filter by Payment Status</label>
                <div class="select-wrapper">
                    <select x-model="filterPaymentStatus">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="partial_payment">Partial Payment</option>
                        <option value="fully_paid">Fully Paid</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label text-xs text-gray-500 uppercase tracking-wider">Filter by Project</label>
                <div class="select-wrapper">
                    <select x-model="filterProject">
                        <option value="">All Projects</option>
                        <template x-for="project in projects" :key="project.id">
                            <option :value="project.id" x-text="project.project_name"></option>
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
                All Deals
                <span class="text-sm font-normal text-gray-400 ml-2" x-text="'(' + filteredDeals.length + ' deals)'"></span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deal #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lead</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="deal in filteredDeals" :key="deal.id">
                        <tr class="border-b hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-700 table-cell-align">
                                <span x-text="'#' + deal.id"></span>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <template x-if="deal.lead">
                                    <span class="font-medium text-slate-700" x-text="deal.lead.full_name"></span>
                                </template>
                                <template x-if="!deal.lead">
                                    <span class="text-gray-400 text-sm">No lead</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <template x-if="deal.project">
                                    <span class="text-gray-700" x-text="deal.project.project_name"></span>
                                </template>
                                <template x-if="!deal.project">
                                    <span class="text-gray-400 text-sm">-</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <template x-if="deal.unit">
                                    <span class="text-gray-700" x-text="deal.unit.unit_number + ' - ' + deal.unit.unit_type"></span>
                                </template>
                                <template x-if="!deal.unit">
                                    <span class="text-gray-400 text-sm">-</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <span class="font-semibold text-slate-700" x-text="formatCurrency(deal.deal_amount) + ' ETB'"></span>
                            </td>
                            <td class="px-6 py-4 table-cell-align">
                                <form method="POST" :action="`{{ url('/updateDealPaymentStatus') }}/${deal.id}`" class="payment-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="payment-status-wrapper">
                                        <select name="payment_status" 
                                                x-model="deal.payment_status"
                                                @change="$el.closest('form').submit()">
                                            <option value="pending">Pending</option>
                                            <option value="partial_payment">Partial</option>
                                            <option value="fully_paid">Fully Paid</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-gray-600 table-cell-align" x-text="formatDate(deal.start_date)"></td>
                            <td class="px-6 py-4 table-cell-align">
                                <div class="action-group">
                                    <!-- View -->
                                    <button @click="openViewModal(deal)" class="action-btn view-btn" title="View Deal Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <!-- Edit -->
                                    <button @click="openEditModal(deal)" class="action-btn edit-btn" title="Edit Deal">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <!-- Delete -->
                                    <button 
                                        @click="
                                            showDeleteModal = true;
                                            deleteDealId = deal.id;
                                            deleteDealReference = '#' + deal.id;
                                            deleteLeadName = deal.lead ? deal.lead.full_name : 'N/A';
                                            deleteUnitNumber = deal.unit ? deal.unit.unit_number : 'N/A';
                                            selectedUnitStatus = deal.unit ? deal.unit.status : 'available';
                                            selectedLeadStage = deal.lead ? deal.lead.current_stage : 'new';
                                        "
                                        class="action-btn delete-btn"
                                        title="Delete Deal"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredDeals.length === 0">
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <span class="block text-3xl mb-2 text-gray-300">📭</span>
                            No deals found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
   <!-- PAGINATION LINKS -->
     {{ $deals->links('vendor.pagination.custom') }}

    <!-- ============================================= -->
    <!-- DELETE MODAL – with inline style to prevent flash -->
    <!-- ============================================= -->
    <div x-show="showDeleteModal" x-cloak
         style="display: none;"
         class="modal-overlay"
         @click.outside="showDeleteModal=false">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">⚠️ Delete Deal</h2>
                    <p class="modal-subtitle">Review the unit &amp; lead statuses before proceeding</p>
                </div>
                <button type="button" @click="showDeleteModal=false" class="modal-close">✕</button>
            </div>

            <div>
                <!-- Enhanced Alert Box -->
                <div class="warning-box">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="warning-box-icon h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="warning-box-title">You are about to permanently delete this deal!</p>
                            <p class="warning-box-text">
                                <strong>Lead:</strong> <span x-text="deleteLeadName"></span><br>
                                <strong>Unit:</strong> <span x-text="deleteUnitNumber"></span>
                            </p>
                            <p class="text-sm text-gray-700 mt-2">
                                Please review the <strong>Unit Status</strong> and <strong>Lead Stage</strong> below.  
                                They are currently set to their existing values – you may change them if needed,  
                                or leave them as is.
                            </p>
                            <p class="text-xs text-red-600 mt-1 font-semibold">This action cannot be undone.</p>
                        </div>
                    </div>
                </div>

                <!-- Unit Status (pre-filled) -->
                <div class="mb-4">
                    <label class="input-label">Unit Status <span class="required">*</span></label>
                    <div class="select-wrapper">
                        <select x-model="selectedUnitStatus" class="input-field" required>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="sold">Sold</option>
                        </select>
                    </div>
                </div>

                <!-- Lead Stage (pre-filled) -->
                <div>
                    <label class="input-label">Lead Stage <span class="required">*</span></label>
                    <div class="select-wrapper">
                        <select x-model="selectedLeadStage" class="input-field" required>
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
            </div>

            <div class="modal-footer">
                <button type="button" @click="showDeleteModal=false" class="btn-secondary">Cancel</button>
                <form method="POST" :action="`{{ url('/deleteDeal') }}/${deleteDealId}`">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="status" x-model="selectedUnitStatus">
                    <input type="hidden" name="current_stage" x-model="selectedLeadStage">
                    <button type="submit" class="btn-danger">Delete Deal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- VIEW MODAL – inline style added               -->
    <!-- ============================================= -->
    <div x-show="viewOpen" x-cloak
         style="display: none;"
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center p-5 z-50">
        <div @click.outside="viewOpen=false" class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold brand flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Deal Details
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Deal #<span x-text="viewDeal?.id"></span>
                    </p>
                </div>
                <button type="button" @click="viewOpen=false" class="text-gray-400 hover:text-gray-600 transition-colors text-xl">✕</button>
            </div>

            <template x-if="viewDeal">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lead</label>
                            <p class="text-lg font-semibold text-slate-800" x-text="viewDeal.lead?.full_name || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Project</label>
                            <p class="text-slate-700" x-text="viewDeal.project?.project_name || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</label>
                            <p class="text-slate-700" x-text="viewDeal.unit?.unit_number + ' - ' + viewDeal.unit?.unit_type || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Collector</label>
                            <p class="text-slate-700" x-text="viewDeal.collector?.full_name || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Status</label>
                            <p><span class="payment-badge" :class="getPaymentBadge(viewDeal.payment_status)" x-text="getPaymentLabel(viewDeal.payment_status)"></span></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Deal Amount</label>
                            <p class="text-2xl font-bold text-emerald-600" x-text="formatCurrency(viewDeal.deal_amount) + ' ETB'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Down Payment</label>
                            <p class="text-slate-700" x-text=" formatCurrency(viewDeal.down_payment) + ' ETB'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Installment Amount</label>
                            <p class="text-slate-700" x-text=" formatCurrency(viewDeal.installment_amount) + ' ETB'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Cycle</label>
                            <p class="text-slate-700 capitalize" x-text="viewDeal.payment_cycle || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Start Date</label>
                            <p class="text-slate-700" x-text="formatDate(viewDeal.start_date)"></p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Number of Installments</label>
                            <p class="text-slate-700" x-text="viewDeal.number_of_installments || 'N/A'"></p>
                        </div>
                    </div>
                    <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Commission Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Commission Type</label>
                                <p class="text-slate-700 capitalize" x-text="viewDeal.commission_type || 'N/A'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Commission Value</label>
                                <p class="text-slate-700" x-text="viewDeal.commission_type === 'percentage' ? viewDeal.commission_value + '%' : 'ETB' + formatCurrency(viewDeal.commission_value)  + 'ETB'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Beneficiary</label>
                                <p class="text-slate-700 capitalize" x-text="viewDeal.beneficiary?.replace('_', ' ') || 'N/A'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                <button type="button" @click="viewOpen=false" class="btn-secondary px-6 py-2.5">Close</button>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- EDIT MODAL – inline style added               -->
    <!-- ============================================= -->
    <div x-show="editOpen" x-cloak
         style="display: none;"
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center p-5 z-50">
        <div @click.outside="editOpen=false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold brand flex items-center gap-2"><span>✎</span> Edit Deal</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Update deal information</p>
                </div>
                <button type="button" @click="editOpen=false" class="text-gray-400 hover:text-gray-600 transition-colors text-xl">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/updateDeal') }}/${editDeal?.id}`">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label required">Deal Amount</label>
                        <input type="number" name="deal_amount" x-model="editDeal.deal_amount" step="0.01" min="0" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Down Payment</label>
                        <input type="number" name="down_payment" x-model="editDeal.down_payment" step="0.01" min="0" required class="form-input" />
                        <p class="text-xs text-gray-400 mt-1">Cannot exceed deal amount</p>
                    </div>
                    <div>
                        <label class="form-label required">Payment Cycle</label>
                        <div class="select-wrapper">
                            <select name="payment_cycle" x-model="editDeal.payment_cycle" required>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="semi_annually">Semi-Annually</option>
                                <option value="annually">Annually</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Number of Installments</label>
                        <input type="number" name="number_of_installments" x-model="editDeal.number_of_installments" min="1" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Start Date</label>
                        <input type="date" name="start_date" x-model="editDeal.start_date" required class="form-input" />
                    </div>
                    <div>
                        <label class="form-label required">Payment Status</label>
                        <div class="select-wrapper">
                            <select name="payment_status" x-model="editDeal.payment_status" required>
                                <option value="pending">Pending</option>
                                <option value="partial_payment">Partial Payment</option>
                                <option value="fully_paid">Fully Paid</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Commission Type</label>
                        <div class="select-wrapper">
                            <select name="commission_type" x-model="editDeal.commission_type" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Commission Value</label>
                        <input type="number" name="commission_value" x-model="editDeal.commission_value" step="0.01" min="0" required class="form-input" placeholder="0.00" />
                    </div>
                    <div>
                        <label class="form-label required">Beneficiary</label>
                        <div class="select-wrapper">
                            <select name="beneficiary" x-model="editDeal.beneficiary" required>
                                <option value="internal_agent">Internal Agent</option>
                                <option value="external_agent">External Agent</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required">Commission Trigger</label>
                        <div class="select-wrapper">
                            <select name="commission_trigger" x-model="editDeal.commission_trigger" required>
                                <option value="immediate">Immediate</option>
                                <option value="each_payment">Each Payment</option>
                                <option value="full_payment">Full Payment</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" @click="editOpen=false" class="btn-secondary px-5 py-2.5">Cancel</button>
                    <button type="submit" class="btn-primary px-6 py-2.5 flex items-center gap-2"><span>✓</span> Update Deal</button>
                </div>
            </form>
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