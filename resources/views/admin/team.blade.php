@extends('layouts.app')
@section('title', 'Manage Teams · NovaTra')
@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
    <style>
        .brand {
            color: #0F286F;
        }

        .brand-bg {
            background-color: #0F286F;
        }

        .brand-border {
            border-color: #0F286F;
        }

        .brand-light-bg {
            background-color: #f0f4ff;
        }

        /* Toast styles */
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
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(0, 0, 0, 0.04);
            border-left: 6px solid #0F286F;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(120%);
            animation: slideIn 0.35s ease forwards;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s;
        }

        .toast-success {
            border-left-color: #0F286F;
        }

        .toast-error {
            border-left-color: #0F286F;
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
            transition: color 0.15s;
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

        /* Role Select Styles */
        .role-select-wrapper {
            position: relative;
            display: inline-block;
        }

        .role-select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
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
            min-width: 90px;
        }

        .role-select-wrapper select:hover {
            border-color: #0F286F;
            background: #f0f4ff;
            box-shadow: 0 2px 8px rgba(15, 40, 111, 0.08);
        }

        .role-select-wrapper select:focus {
            outline: none;
            border-color: #0F286F;
            box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
        }

        .role-select-wrapper::after {
            content: '▾';
            position: absolute;
            right: 0.7rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.6rem;
            color: #94a3b8;
            pointer-events: none;
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
            width: 20px;
            height: 20px;
        }

        .action-group {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        /* Agents Badge */
        .agents-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
            background: #f0f4ff;
            color: #0F286F;
            border: 1px solid #0F286F;
        }

        .agents-badge .count {
            font-weight: 600;
            color: #0F286F;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            transition: color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #0F286F;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        /* Modal */
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
        }

        .modal-close:hover {
            color: #0F286F;
        }

        .modal-footer {
            border-top: 2px solid #0F286F;
            padding-top: 1rem;
            margin-top: 1.5rem;
        }

        .btn-primary {
            background-color: #0F286F;
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.15s;
            border: 2px solid #0F286F;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 40, 111, 0.2);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background-color: white;
            color: #0F286F;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.15s;
            border: 2px solid #0F286F;
        }

        .btn-secondary:hover {
            background-color: #f0f4ff;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #dc2626;
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.15s;
            border: 2px solid #dc2626;
        }

        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }

        .btn-danger:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .info-box {
            background-color: #f0f4ff;
            border: 2px solid #0F286F;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .info-box-icon {
            color: #0F286F;
            flex-shrink: 0;
        }

        .info-box-title {
            color: #0F286F;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .info-box-text {
            color: #1e293b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .input-field {
            width: 100%;
            border: 2px solid #0F286F;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.15s;
            outline: none;
        }

        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.15);
        }

        .input-field.input-error {
            border-color: #dc2626;
        }

        .input-field.input-error:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
        }

        .input-label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #0F286F;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        .spinner-dark {
            border: 2px solid #0F286F;
            border-top-color: transparent;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .hidden {
            display: none !important;
        }

        .text-brand {
            color: #0F286F;
        }
    </style>
@endpush

@section('content')

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="p-6 md:p-10">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold brand">Manage Teams</h1>
                <p class="text-gray-500 mt-2">Create and manage admins, supervisors, and sales agents.</p>
            </div>
            <button onclick="document.getElementById('addUserModal').classList.add('active')" 
                    class="brand-bg text-white px-6 py-3 rounded-xl hover:opacity-90 transition">
                + Add User
            </button>
        </div>

        <!-- TEAM MEMBERS TABLE -->
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Team Members</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supervisor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Assigned Agents</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium">{{ $team->full_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $team->email }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('updateRole', $team->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <div class="role-select-wrapper">
                                        <select name="role" onchange="this.form.submit()">
                                            <option value="admin" {{ $team->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="supervisor" {{ $team->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="agent" {{ $team->role == 'agent' ? 'selected' : '' }}>Agent</option>
                                            <option value="collector" {{ $team->role == 'collector' ? 'selected' : '' }}>Collector</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium">{{ $team->monthly_target ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($team->supervisor)
                                <span class="text-sm font-medium">{{ $team->supervisor->full_name }}</span>
                                @else
                                <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($team->role == 'supervisor')
                                <span class="agents-badge">
                                    <span class="count">{{ $team->agents->count() }}</span>
                                    agent{{ $team->agents->count() != 1 ? 's' : '' }}
                                </span>
                                @else
                                <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="action-group">
                                    <button 
                                        onclick="openEditModal({{ json_encode([
                                            'id' => $team->id,
                                            'full_name' => $team->full_name,
                                            'email' => $team->email,
                                            'role' => $team->role,
                                            'monthly_target' => $team->monthly_target ?? 0,
                                            'supervisor_id' => $team->supervisor_id
                                        ]) }})"
                                        class="action-btn edit-btn"
                                        title="Edit User"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button 
                                        onclick="confirmDelete({{ $team->id }}, '{{ $team->full_name }}', {{ $team->leads()->exists() ? 'true' : 'false' }})"
                                        class="action-btn delete-btn"
                                        title="Delete User"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400">No team members yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div class="modal-overlay" id="deleteModal">
            <div class="modal-content">
                <div class="modal-header flex justify-between items-center">
                    <div>
                        <h2 class="modal-title">Delete User</h2>
                        <p class="modal-subtitle">Confirm user deletion</p>
                    </div>
                    <button type="button" onclick="closeModal('deleteModal')" class="modal-close">✕</button>
                </div>

                <div class="info-box">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="info-box-icon h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="info-box-title">Are you sure you want to delete this user?</p>
                            <p class="info-box-text">
                                User: <span class="font-semibold" id="deleteUserName"></span>
                            </p>
                            <p class="text-xs text-gray-500 mt-2">This action cannot be undone. All associated data will be permanently removed.</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer flex justify-end gap-3">
                    <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-danger" id="deleteButton">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- REASSIGNMENT MODAL FOR DELETE -->
        <div class="modal-overlay" id="reassignModal">
            <div class="modal-content">
                <div class="modal-header flex justify-between items-center">
                    <div>
                        <h2 class="modal-title">Reassign Leads Required</h2>
                        <p class="modal-subtitle">This user has leads assigned</p>
                    </div>
                    <button type="button" onclick="closeModal('reassignModal')" class="modal-close">✕</button>
                </div>

                <div class="info-box mb-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="info-box-icon h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="info-box-title">This user has leads assigned to them.</p>
                            <p class="info-box-text">Please select a new agent to reassign these leads before deletion.</p>
                        </div>
                    </div>
                </div>

                <form id="reassignForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="reassignUserId">

                    <div class="mb-4">
                        <label class="input-label">New Agent *</label>
                        <select name="new_agent" id="newAgentSelect" class="input-field" required>
                            <option value="">Select Agent</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->full_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Select an agent to receive the leads from this user.</p>
                    </div>

                    <div id="reassignError" class="hidden mb-4 p-3 bg-red-50 border-2 border-red-500 rounded-lg text-red-700 text-sm"></div>

                    <div class="modal-footer flex justify-end gap-3">
                        <button type="button" onclick="closeModal('reassignModal')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" id="reassignSubmitBtn" class="btn-primary">
                            Reassign & Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- REASSIGN SUPERVISOR MODAL -->
        @if(session('error') && session('supervisor_id') && session('agents'))
        <div class="modal-overlay active" id="reassignSupervisorModal">
            <div class="modal-content wide">
                <div class="modal-header flex justify-between items-center">
                    <div>
                        <h2 class="modal-title">Reassign Agents</h2>
                        <p class="modal-subtitle">Select a new supervisor for these agents</p>
                    </div>
                    <button type="button" onclick="document.getElementById('reassignSupervisorModal').classList.remove('active')" class="modal-close">✕</button>
                </div>

                <div class="info-box mb-4">
                    <p class="info-box-title">The following agents will be reassigned:</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach(session('agents') as $agent)
                        <span class="text-xs px-3 py-1.5 rounded-full font-medium brand-bg text-white">
                            {{ $agent['full_name'] }}
                        </span>
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('changeSupervisors') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="old_supervisor" value="{{ session('supervisor_id') }}">

                    <div class="mb-4">
                        <label class="input-label">New Supervisor *</label>
                        <select name="new_supervisor" class="input-field" required>
                            <option value="">Select Supervisor</option>
                            @foreach($supervisors as $supervisor)
                            @if(session('supervisor_id') && $supervisor->id == session('supervisor_id'))
                            @continue
                            @endif
                            <option value="{{ $supervisor->id }}">{{ $supervisor->full_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Note: The new supervisor must have the "supervisor" role.</p>
                    </div>

                    <div class="mb-4">
                        <label class="input-label">New Role for Old Supervisor *</label>
                        <select name="new_role" class="input-field" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="agent">Agent</option>
                        </select>
                    </div>

                    <div class="modal-footer flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('reassignSupervisorModal').classList.remove('active')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            Reassign Agents
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- EDIT USER MODAL -->
        <div class="modal-overlay" id="editModal">
            <div class="modal-content wide">
                <div class="modal-header flex justify-between items-center">
                    <div>
                        <h2 class="modal-title">Edit User</h2>
                        <p class="modal-subtitle">Update user information</p>
                    </div>
                    <button type="button" onclick="closeModal('editModal')" class="modal-close">✕</button>
                </div>

                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Full Name *</label>
                            <input type="text" name="full_name" id="editFullName" class="input-field" required>
                        </div>

                        <div>
                            <label class="input-label">Email *</label>
                            <input type="email" name="email" id="editEmail" class="input-field" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="input-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="editPassword" class="input-field">
                                <button type="button" class="password-toggle" onclick="togglePassword('editPassword', 'editPasswordToggle')">
                                    <svg id="editPasswordToggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Leave blank to keep current password</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="input-label">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" id="editPasswordConfirmation" class="input-field">
                                <button type="button" class="password-toggle" onclick="togglePassword('editPasswordConfirmation', 'editPasswordConfirmToggle')">
                                    <svg id="editPasswordConfirmToggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Monthly Target</label>
                            <input type="number" name="monthly_target" id="editMonthlyTarget" class="input-field">
                        </div>

                        <div class="md:col-span-2">
                            <label class="input-label">Supervisor</label>
                            <select name="supervisor_id" id="editSupervisorId" class="input-field">
                                <option value="">No Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->full_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Select a supervisor for agents. Admins and supervisors don't need a supervisor.</p>
                        </div>
                    </div>

                    <div class="modal-footer flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editModal')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADD USER MODAL -->
        <div class="modal-overlay" id="addUserModal">
            <div class="modal-content wide">
                <div class="modal-header flex justify-between items-center">
                    <div>
                        <h2 class="modal-title">Add User</h2>
                        <p class="modal-subtitle">Create a new team member</p>
                    </div>
                    <button type="button" onclick="closeModal('addUserModal')" class="modal-close">✕</button>
                </div>

                <form method="POST" action="{{ route('createUser') }}">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Full Name *</label>
                            <input name="full_name" value="{{ old('full_name') }}" class="input-field" required>
                        </div>

                        <div>
                            <label class="input-label">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="input-field" required>
                        </div>

                        <div>
                            <label class="input-label">Password *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="addPassword" class="input-field" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('addPassword', 'addPasswordToggle')">
                                    <svg id="addPasswordToggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Confirm Password *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" id="addPasswordConfirmation" class="input-field" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('addPasswordConfirmation', 'addPasswordConfirmToggle')">
                                    <svg id="addPasswordConfirmToggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Role *</label>
                            <select name="role" id="addRole" class="input-field" required>
                                <option value="admin">Admin</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="agent" selected>Agent</option>
                                <option value="collector" selected>Collector</option>
                            </select>
                        </div>

                        <div>
                            <label class="input-label">Monthly Target</label>
                            <input type="number" name="monthly_target" value="{{ old('monthly_target', 0) }}" class="input-field">
                        </div>

                        <div class="md:col-span-2" id="supervisorField">
                            <label class="input-label">Supervisor</label>
                            <select name="supervisor_id" class="input-field">
                                <option value="">Select Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->full_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Required for agents. Optional for supervisors.</p>
                        </div>
                    </div>

                    <div class="modal-footer flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addUserModal')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toast System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <span class="toast-icon">${type === 'success' ? '✓' : '✕'}</span>
                <span class="toast-message">${message}</span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            `;
            container.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.add('toast-exit');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        // Modal Functions
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Password Toggle
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Role selection for add user
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('addRole');
            const supervisorField = document.getElementById('supervisorField');
            
            if (roleSelect) {
                roleSelect.addEventListener('change', function() {
                    if (this.value === 'agent') {
                        supervisorField.style.display = 'block';
                    } else {
                        supervisorField.style.display = 'none';
                    }
                });
                // Trigger on load
                roleSelect.dispatchEvent(new Event('change'));
            }
        });

        // Delete functionality
        let deleteUserId = null;

        function confirmDelete(userId, userName, hasLeads) {
            deleteUserId = userId;
            document.getElementById('deleteUserName').textContent = userName;
            
            if (hasLeads) {
                // User has leads - show reassignment modal directly
                document.getElementById('reassignUserId').value = userId;
                document.getElementById('reassignForm').action = `/deleteUser/${userId}`;
                // Filter out the user being deleted from dropdown
                const select = document.getElementById('newAgentSelect');
                for (let option of select.options) {
                    if (option.value == userId) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                }
                // Reset the select to empty
                select.value = '';
                document.getElementById('reassignError').classList.add('hidden');
                openModal('reassignModal');
            } else {
                // No leads - show delete confirmation
                document.getElementById('deleteForm').action = `/deleteUser/${userId}`;
                openModal('deleteModal');
            }
        }

        // Handle reassign form submission with traditional form submit
        document.addEventListener('DOMContentLoaded', function() {
            const reassignForm = document.getElementById('reassignForm');
            if (reassignForm) {
                reassignForm.addEventListener('submit', function(e) {
                    const newAgent = document.getElementById('newAgentSelect').value;
                    const errorDiv = document.getElementById('reassignError');
                    
                    if (!newAgent) {
                        e.preventDefault();
                        errorDiv.textContent = 'Please select a new agent.';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    // Form will submit normally
                    errorDiv.classList.add('hidden');
                });
            }
        });

        // Edit User Function
        function openEditModal(user) {
            document.getElementById('editForm').action = `/updateUser/${user.id}`;
            document.getElementById('editFullName').value = user.full_name || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editMonthlyTarget').value = user.monthly_target || 0;
            document.getElementById('editSupervisorId').value = user.supervisor_id || '';
            // Clear password fields
            document.getElementById('editPassword').value = '';
            document.getElementById('editPasswordConfirmation').value = '';
            openModal('editModal');
        }

        // Show session messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error') && !session('supervisor_id'))
            showToast('{{ session('error') }}', 'error');
        @endif

        @if($errors->any())
            showToast('{{ $errors->first() }}', 'error');
        @endif
    </script>

@stop