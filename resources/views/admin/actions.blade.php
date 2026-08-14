@extends('layouts.app')

@section('title', 'Actions Management · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@push('styles')
<style>
    /* Alpine.js cloaking – ensures elements are hidden until Alpine initializes */
    [x-cloak] { display: none !important; }

    /* Toast slide animations */
    .toast {
        transform: translateX(120%);
        animation: slideIn 0.35s ease forwards;
    }
    .toast-exit {
        animation: slideOut 0.3s ease forwards;
    }
    @keyframes slideIn {
        0% { opacity: 0; transform: translateX(120%); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        0% { opacity: 1; transform: translateX(0); }
        100% { opacity: 0; transform: translateX(120%); }
    }

    /* Custom select arrow – can't be done with Tailwind alone */
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
        box-shadow: 0 0 0 3px rgba(15,40,111,0.15);
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

    /* Required field asterisk */
    .required::after {
        content: " *";
        color: #ef4444;
    }
</style>
@endpush

@section('content')

<!-- Toast Container -->
<div class="fixed bottom-8 right-8 z-[9999] flex flex-col gap-3 max-w-[380px] w-full pointer-events-none" x-data="toastManager()" x-init="init()" @toast.window="addToast($event.detail)">
    <template x-for="(toast, index) in toasts" :key="index">
        <div class="toast pointer-events-auto px-6 py-4 rounded-2xl bg-white shadow-xl border-l-[6px] flex items-center gap-3 text-sm text-slate-800"
             :class="{
                'border-l-[#0F286F]': toast.type === 'success',
                'border-l-[#0F286F]': toast.type === 'error'
             }"
             x-init="setTimeout(() => removeToast(index), 5000)">
            <span class="text-xl leading-none" x-text="toast.type === 'success' ? '✓' : '✕'"></span>
            <span class="flex-1 font-medium" x-text="toast.message"></span>
            <button class="bg-transparent border-none text-gray-400 hover:text-gray-600 text-xl leading-none cursor-pointer" @click="removeToast(index)">✕</button>
        </div>
    </template>
</div>

<div 
    x-data="{
        // Modal flags – all default to false
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        viewOpen: false,
        
        // Data
        actions: {{ json_encode($actions->items()) }},
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
        
        // Minimum datetime for scheduled_time picker
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
            return status === 'done' 
                ? 'bg-emerald-100 text-emerald-800' 
                : 'bg-amber-100 text-amber-800';
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
        },
        getAssignedUserName(action) {
            // 1. Try the eager-loaded relation, whatever it's called (assignedUser / assigned_user)
            if (action.assignedUser && action.assignedUser.full_name) {
                return action.assignedUser.full_name;
            }
            if (action.assigned_user && action.assigned_user.full_name) {
                return action.assigned_user.full_name;
            }
            // 2. Fall back to looking the id up in the users list already loaded on this page
            if (action.assigned_to) {
                const match = this.users.find(u => String(u.id) === String(action.assigned_to));
                if (match) return match.full_name;
            }
            return 'N/A';
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
            <h1 class="text-3xl font-bold text-[#0F286F]">Actions Management</h1>
            <p class="text-gray-500 mt-1 text-sm">Track and manage all lead actions across your team.</p>
        </div>
        <div>
            <button @click="openCreateModal()" class="bg-[#0F286F] text-white px-5 py-2.5 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2 border-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Action
            </button>
        </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5 mb-6">
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-all relative overflow-hidden">
            <div class="absolute top-0 left-0 w-10 h-1 bg-[#0F286F] rounded-br-lg"></div>
            <div class="text-3xl sm:text-4xl font-bold text-[#0b1b3a]" x-text="totalActions"></div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Actions</div>
            <div class="text-xs text-gray-400 mt-0.5">All activities</div>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-all relative overflow-hidden">
            <div class="absolute top-0 left-0 w-10 h-1 bg-emerald-600 rounded-br-lg"></div>
            <div class="text-3xl sm:text-4xl font-bold text-[#0b1b3a]" x-text="doneActions"></div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Done</div>
            <div class="text-xs text-gray-400 mt-0.5">Completed actions</div>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-all relative overflow-hidden">
            <div class="absolute top-0 left-0 w-10 h-1 bg-amber-600 rounded-br-lg"></div>
            <div class="text-3xl sm:text-4xl font-bold text-[#0b1b3a]" x-text="onProgressActions"></div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">On Progress</div>
            <div class="text-xs text-gray-400 mt-0.5">Pending actions</div>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-all relative overflow-hidden">
            <div class="absolute top-0 left-0 w-10 h-1 bg-purple-600 rounded-br-lg"></div>
            <div class="text-3xl sm:text-4xl font-bold text-[#0b1b3a]" x-text="assignedToMe"></div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Assigned to Me</div>
            <div class="text-xs text-gray-400 mt-0.5">Your tasks</div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Activity Type</label>
                <div class="select-wrapper">
                    <select x-model="filterActivityType" class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                        <option value="">All Types</option>
                        <option value="follow_up_call">Follow-up Call</option>
                        <option value="meeting">Meeting</option>
                        <option value="property_visit">Property Visit</option>
                        <option value="email">Email</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                <div class="select-wrapper">
                    <select x-model="filterStatus" class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                        <option value="">All Statuses</option>
                        <option value="done">Done</option>
                        <option value="on_progress">On Progress</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Assigned To</label>
                <div class="select-wrapper">
                    <select x-model="filterAssignedTo" class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
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
                    class="bg-white border border-gray-200 rounded-full px-4 py-1.5 text-xs font-medium text-gray-600 hover:border-[#0F286F] hover:text-[#0F286F] hover:bg-[#f8faff] transition flex items-center gap-1.5 cursor-pointer"
                    :class="{ 'opacity-50 pointer-events-none': !isFiltered() }"
                    :disabled="!isFiltered()"
                >
                    <span>↺</span> Clear filters
                </button>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
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
                                    <div class="relative inline-block">
                                        <select name="activity_type" onchange="this.form.submit()" 
                                                class="appearance-none border border-gray-200 rounded-full py-1 pl-3 pr-7 text-xs font-medium bg-white hover:border-[#0F286F] hover:bg-[#f8faff] focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 cursor-pointer min-w-[110px]">
                                            <option value="follow_up_call" :selected="action.activity_type === 'follow_up_call'">Follow-up Call</option>
                                            <option value="meeting" :selected="action.activity_type === 'meeting'">Meeting</option>
                                            <option value="property_visit" :selected="action.activity_type === 'property_visit'">Property Visit</option>
                                            <option value="email" :selected="action.activity_type === 'email'">Email</option>
                                        </select>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none">▾</span>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700" x-text="getAssignedUserName(action)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <!-- INLINE UPDATE FOR STATUS -->
                                <form method="POST" :action="`{{ url('/updateActionStatus') }}/${action.id}`" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <div class="relative inline-block">
                                        <select name="status" onchange="this.form.submit()" 
                                                class="appearance-none border border-gray-200 rounded-full py-1 pl-3 pr-7 text-xs font-medium bg-white hover:border-[#0F286F] hover:bg-[#f8faff] focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 cursor-pointer min-w-[110px]">
                                            <option value="on_progress" :selected="action.status === 'on_progress'">On Progress</option>
                                            <option value="done" :selected="action.status === 'done'">Done</option>
                                        </select>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none">▾</span>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm" x-text="formatDate(action.scheduled_time)"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <!-- Edit -->
                                    <button @click="openEditModal(action)" class="p-1.5 rounded-full text-gray-400 hover:text-[#0F286F] hover:bg-[#f0f4ff] transition-colors" title="Edit Action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <!-- Delete -->
                                    <button @click="openDeleteModal(action)" class="p-1.5 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete Action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

    <!-- PAGINATION LINKS -->
     {{ $actions->links('vendor.pagination.custom') }}

    <!-- ============================================= -->
    <!-- CREATE ACTION MODAL                           -->
    <!-- ============================================= -->
    <div x-show="createOpen" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-5">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl border-2 border-[#0F286F] p-6 max-h-[90vh] overflow-y-auto" @click.outside="createOpen=false">
            <div class="border-b-2 border-[#0F286F] pb-4 mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-[#0F286F]">➕ Create Action</h2>
                    <p class="text-sm text-gray-500">Add a new action for a lead</p>
                </div>
                <button type="button" @click="createOpen=false" class="text-gray-400 hover:text-[#0F286F] text-2xl leading-none bg-transparent border-none cursor-pointer">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/createAction') }}/${newAction.lead_id}`" @submit.prevent="
                if (!newAction.lead_id) {
                    alert('Please select a lead.');
                    return;
                }
                $el.submit();
            ">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Lead -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Lead</label>
                        <div class="select-wrapper">
                            <select name="lead_id" x-model="newAction.lead_id" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="">Select Lead</option>
                                <template x-for="lead in leads" :key="lead.id">
                                    <option :value="lead.id" x-text="lead.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Activity Type</label>
                        <div class="select-wrapper">
                            <select name="activity_type" x-model="newAction.activity_type" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
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
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Assigned To</label>
                        <div class="select-wrapper">
                            <select name="assigned_to" x-model="newAction.assigned_to" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="">Select User</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Status</label>
                        <div class="select-wrapper">
                            <select name="status" x-model="newAction.status" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="on_progress">On Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scheduled Time -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_time" x-model="newAction.scheduled_time" 
                               :min="minDateTime" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5">Description</label>
                        <textarea name="description" x-model="newAction.description" rows="2" class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20" placeholder="Optional notes..."></textarea>
                    </div>
                </div>

                <div class="border-t-2 border-[#0F286F] pt-4 mt-6 flex justify-end gap-3">
                    <button type="button" @click="createOpen=false" class="bg-white text-[#0F286F] border-2 border-[#0F286F] px-6 py-2.5 rounded-xl font-medium hover:bg-[#f0f4ff] transition cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-[#0F286F] text-white px-6 py-2.5 rounded-xl font-medium hover:opacity-90 transition border-none cursor-pointer">Create Action</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- EDIT ACTION MODAL                             -->
    <!-- ============================================= -->
    <div x-show="editOpen" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-5">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl border-2 border-[#0F286F] p-6 max-h-[90vh] overflow-y-auto" @click.outside="editOpen=false">
            <div class="border-b-2 border-[#0F286F] pb-4 mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-[#0F286F]">✎ Edit Action</h2>
                    <p class="text-sm text-gray-500">Update action details</p>
                </div>
                <button type="button" @click="editOpen=false" class="text-gray-400 hover:text-[#0F286F] text-2xl leading-none bg-transparent border-none cursor-pointer">✕</button>
            </div>

            <form method="POST" :action="`{{ url('/updateAction') }}/${editForm.id}`">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Lead -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Lead</label>
                        <div class="select-wrapper">
                            <select name="lead_id" x-model="editForm.lead_id" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="">Select Lead</option>
                                <template x-for="lead in leads" :key="lead.id">
                                    <option :value="lead.id" x-text="lead.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Activity Type</label>
                        <div class="select-wrapper">
                            <select name="activity_type" x-model="editForm.activity_type" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="follow_up_call">Follow-up Call</option>
                                <option value="meeting">Meeting</option>
                                <option value="property_visit">Property Visit</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Assigned To</label>
                        <div class="select-wrapper">
                            <select name="assigned_to" x-model="editForm.assigned_to" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="">Select User</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.full_name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Status</label>
                        <div class="select-wrapper">
                            <select name="status" x-model="editForm.status" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20 appearance-none">
                                <option value="on_progress">On Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scheduled Time -->
                    <div>
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5 required">Scheduled Time</label>
                        <input type="datetime-local" name="scheduled_time" x-model="editForm.scheduled_time" 
                               :min="minDateTime" required class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#0F286F] mb-1.5">Description</label>
                        <textarea name="description" x-model="editForm.description" rows="2" class="w-full border-2 border-[#0F286F] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0F286F]/20" placeholder="Optional notes..."></textarea>
                    </div>
                </div>

                <div class="border-t-2 border-[#0F286F] pt-4 mt-6 flex justify-end gap-3">
                    <button type="button" @click="editOpen=false" class="bg-white text-[#0F286F] border-2 border-[#0F286F] px-6 py-2.5 rounded-xl font-medium hover:bg-[#f0f4ff] transition cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-[#0F286F] text-white px-6 py-2.5 rounded-xl font-medium hover:opacity-90 transition border-none cursor-pointer">Update Action</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- DELETE ACTION MODAL                           -->
    <!-- ============================================= -->
    <div x-show="deleteOpen" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-5">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border-2 border-[#0F286F] p-6 max-h-[90vh] overflow-y-auto" @click.outside="deleteOpen=false">
            <div class="border-b-2 border-[#0F286F] pb-4 mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-[#0F286F]">⚠️ Delete Action</h2>
                    <p class="text-sm text-gray-500">This action will be permanently removed</p>
                </div>
                <button type="button" @click="deleteOpen=false" class="text-gray-400 hover:text-[#0F286F] text-2xl leading-none bg-transparent border-none cursor-pointer">✕</button>
            </div>

            <div>
                <div class="bg-red-50 border-2 border-red-600 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-red-600 font-bold text-sm">You are about to delete this action!</p>
                            <p class="text-sm text-slate-700 mt-0.5">
                                <strong>Lead:</strong> <span x-text="deleteLeadName"></span><br>
                                <strong>Activity:</strong> <span x-text="getActivityLabel(deleteActivityType)"></span>
                            </p>
                            <p class="text-xs text-red-600 font-semibold mt-1">This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t-2 border-[#0F286F] pt-4 mt-6 flex justify-end gap-3">
                <button type="button" @click="deleteOpen=false" class="bg-white text-[#0F286F] border-2 border-[#0F286F] px-6 py-2.5 rounded-xl font-medium hover:bg-[#f0f4ff] transition cursor-pointer">Cancel</button>
                <form method="POST" :action="`{{ url('/deleteAction') }}/${deleteActionId}`">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-6 py-2.5 rounded-xl font-medium hover:opacity-90 transition border-none cursor-pointer">Delete Action</button>
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