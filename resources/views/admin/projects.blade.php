@extends('layouts.app')

@section('title', 'Projects · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    /* Modal styles */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    .modal-overlay.active {
        opacity: 1;
        pointer-events: all;
    }
    
    .modal-content {
        background: white;
        border-radius: 1.5rem;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }
    
    .modal-overlay.active .modal-content {
        transform: scale(1);
    }
    
    .project-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        background: white;
        position: relative;
    }
    
    .project-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 40, 111, 0.08);
        border-color: #0F286F;
    }
    
    .project-card-actions {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .project-card:hover .project-card-actions {
        opacity: 1;
    }
    
    .project-card-actions button {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #4b5563;
    }
    
    .project-card-actions button:hover {
        background: #0F286F;
        color: white;
        border-color: #0F286F;
    }
    
    .project-card-actions .edit-btn:hover {
        background: #0F286F;
    }
    
    .project-card-actions .delete-btn:hover {
        background: #dc2626;
        border-color: #dc2626;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .status-completed {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .btn-primary {
        background-color: #0F286F;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: transparent;
        color: #0F286F;
        border: 2px solid #0F286F;
        padding: 0.625rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: background 0.15s ease;
        cursor: pointer;
    }
    
    .btn-secondary:hover {
        background: #f8faff;
    }
    
    .form-input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.15s ease;
        outline: none;
    }
    
    .form-input:focus {
        border-color: #0F286F;
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.1);
    }
    
    .form-input-error {
        border-color: #dc2626;
    }
    
    .form-input-error:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #0F286F;
    }
    
    .form-error {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #f1f4f9;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0F286F;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    .progress-bar {
        width: 100%;
        background: #e5e7eb;
        border-radius: 9999px;
        height: 0.5rem;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background-color: #0F286F;
        border-radius: 9999px;
        transition: width 0.5s ease;
        width: 0%;
    }
    
    /* Toast notification */
    .toast {
        position: fixed;
        top: 2rem;
        right: 2rem;
        z-index: 9999;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        background: white;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        border-left: 4px solid #0F286F;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        max-width: 400px;
    }
    
    .toast.show {
        transform: translateX(0);
    }
    
    .toast-success {
        border-left-color: #22c55e;
    }
    
    .toast-error {
        border-left-color: #ef4444;
    }
    
    .toast-message {
        color: #1e293b;
        font-size: 0.95rem;
    }

    /* Delete confirmation modal */
    .delete-modal-content {
        max-width: 450px;
        text-align: center;
    }

    .delete-icon {
        font-size: 3rem;
        color: #dc2626;
        margin-bottom: 1rem;
    }

    /* Error summary in modal */
    .error-summary {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: none;
    }
    
    .error-summary.show {
        display: block;
    }
    
    .error-summary-title {
        color: #dc2626;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .error-summary-list {
        color: #991b1b;
        font-size: 0.875rem;
        list-style-type: disc;
        padding-left: 1.25rem;
        margin-top: 0.25rem;
    }
    
    .error-summary-list li {
        margin-top: 0.125rem;
    }
</style>
@endpush

@section('content')

<div class="p-6 md:p-10">
    
    <!-- Toast Notification -->
    @if(session('success'))
    <div id="toast" class="toast toast-success show">
        <div class="toast-message">{{ session('success') }}</div>
    </div>
    @endif
    
    @if(session('error'))
    <div id="toast" class="toast toast-error show">
        <div class="toast-message">{{ session('error') }}</div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold" style="color: #0F286F;">Projects</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage your construction projects and track progress.</p>
        </div>
        <button onclick="openModal('createModal')" 
                class="text-white px-6 py-3 rounded-xl hover:opacity-90 transition flex items-center gap-2 shadow-md"
                style="background-color: #0F286F;">
            <span>+</span> New Project
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <div class="stat-value">{{ $projects->count() }}</div>
            <div class="stat-label">Total Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $projects->where('status', 'active')->count() + $projects->whereNull('status')->count() }}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $projects->where('status', 'completed')->count() }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $projects->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
        <div class="project-card">
            <div class="project-card-actions">
                <button onclick="openEditModal({{ $project->id }})" class="edit-btn">✎ Edit</button>
                <button onclick="confirmDelete({{ $project->id }})" class="delete-btn">✕</button>
            </div>
            
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold text-gray-800">{{ $project->project_name }}</h3>
                @if($project->status == 'completed')
                <span class="status-badge status-completed">Completed</span>
                @elseif($project->status == 'pending')
                <span class="status-badge status-pending">Pending</span>
                @else
                <span class="status-badge status-active">Active</span>
                @endif
            </div>
            
            <p class="text-sm text-gray-600 mb-2">{{ $project->location_address ?? 'N/A' }}</p>
            
            <div class="flex items-center gap-4 text-sm text-gray-500 mt-3">
                <span>Manager: {{ $project->project_manager ?? 'Not assigned' }}</span>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-medium" style="color: #0F286F;">
                        {{ $project->total_floors ? round(($project->completed_floors ?? 0) / $project->total_floors * 100) : 0 }}%
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: {{ $project->total_floors ? round(($project->completed_floors ?? 0) / $project->total_floors * 100) : 0 }}%;"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>{{ $project->completed_floors ?? 0 }} floors completed</span>
                    <span>{{ $project->total_floors ?? 0 }} total floors</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400">Due: {{ $project->due_date ?? 'N/A' }}</span>
                <span class="text-xs text-gray-400">Units: {{ $project->total_units ?? 0 }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-400 text-lg">No projects found</p>
            <p class="text-gray-400 text-sm mt-1">Click "New Project" to create your first project</p>
        </div>
        @endforelse
    </div>

    <!-- Create Project Modal -->
    <div id="createModal" class="modal-overlay @if($errors->any() && !session('edit_error')) active @endif">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold" style="color: #0F286F;">Create New Project</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Enter project details below</p>
                </div>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form action="{{ route('createProject') }}" method="POST" class="space-y-6" id="createForm">
                @csrf
                
                <!-- Error Summary -->
                @if($errors->any() && !session('edit_error'))
                <div class="error-summary show">
                    <div class="error-summary-title">Please fix the following errors:</div>
                    <ul class="error-summary-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Project Name -->
                    <div>
                        <label for="project_name" class="form-label">
                            Project Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="project_name"
                            type="text"
                            name="project_name"
                            value="{{ old('project_name') }}"
                            class="form-input @error('project_name') form-input-error @enderror"
                            placeholder="Enter project name"
                            required>
                        @error('project_name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Project Manager -->
                    <div>
                        <label for="project_manager" class="form-label">
                            Project Manager
                        </label>
                        <input
                            id="project_manager"
                            type="text"
                            name="project_manager"
                            value="{{ old('project_manager') }}"
                            class="form-input @error('project_manager') form-input-error @enderror"
                            placeholder="Manager name">
                        @error('project_manager')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location Address -->
                    <div class="md:col-span-2">
                        <label for="location_address" class="form-label">
                            Location Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="location_address"
                            type="text"
                            name="location_address"
                            value="{{ old('location_address') }}"
                            class="form-input @error('location_address') form-input-error @enderror"
                            placeholder="Project location"
                            required>
                        @error('location_address')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Floors -->
                    <div>
                        <label for="total_floors" class="form-label">
                            Total Floors <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="total_floors"
                            type="number"
                            name="total_floors"
                            value="{{ old('total_floors') }}"
                            class="form-input @error('total_floors') form-input-error @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_floors')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Completed Floors -->
                    <div>
                        <label for="completed_floors" class="form-label">
                            Completed Floors
                        </label>
                        <input
                            id="completed_floors"
                            type="number"
                            name="completed_floors"
                            value="{{ old('completed_floors', 0) }}"
                            class="form-input @error('completed_floors') form-input-error @enderror"
                            placeholder="0"
                            min="0">
                        @error('completed_floors')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Units -->
                    <div>
                        <label for="total_units" class="form-label">
                            Total Units <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="total_units"
                            type="number"
                            name="total_units"
                            value="{{ old('total_units') }}"
                            class="form-input @error('total_units') form-input-error @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_units')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="form-label">
                            Due Date
                        </label>
                        <input
                            id="due_date"
                            type="date"
                            name="due_date"
                            value="{{ old('due_date') }}"
                            class="form-input @error('due_date') form-input-error @enderror">
                        @error('due_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="form-label">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="form-input @error('status') form-input-error @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('createModal')" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editModal" class="modal-overlay @if($errors->any() && session('edit_error')) active @endif">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold" style="color: #0F286F;">Edit Project</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Update project details</p>
                </div>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form id="editForm" action="" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Error Summary -->
                @if($errors->any() && session('edit_error'))
                <div class="error-summary show">
                    <div class="error-summary-title">Please fix the following errors:</div>
                    <ul class="error-summary-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Project Name -->
                    <div>
                        <label for="edit_project_name" class="form-label">
                            Project Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_project_name"
                            type="text"
                            name="project_name"
                            class="form-input @error('project_name') form-input-error @enderror"
                            placeholder="Enter project name"
                            required>
                        @error('project_name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Project Manager -->
                    <div>
                        <label for="edit_project_manager" class="form-label">
                            Project Manager
                        </label>
                        <input
                            id="edit_project_manager"
                            type="text"
                            name="project_manager"
                            class="form-input @error('project_manager') form-input-error @enderror"
                            placeholder="Manager name">
                        @error('project_manager')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location Address -->
                    <div class="md:col-span-2">
                        <label for="edit_location_address" class="form-label">
                            Location Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_location_address"
                            type="text"
                            name="location_address"
                            class="form-input @error('location_address') form-input-error @enderror"
                            placeholder="Project location"
                            required>
                        @error('location_address')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Floors -->
                    <div>
                        <label for="edit_total_floors" class="form-label">
                            Total Floors <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_total_floors"
                            type="number"
                            name="total_floors"
                            class="form-input @error('total_floors') form-input-error @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_floors')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Completed Floors -->
                    <div>
                        <label for="edit_completed_floors" class="form-label">
                            Completed Floors
                        </label>
                        <input
                            id="edit_completed_floors"
                            type="number"
                            name="completed_floors"
                            class="form-input @error('completed_floors') form-input-error @enderror"
                            placeholder="0"
                            min="0">
                        @error('completed_floors')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Units -->
                    <div>
                        <label for="edit_total_units" class="form-label">
                            Total Units <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_total_units"
                            type="number"
                            name="total_units"
                            class="form-input @error('total_units') form-input-error @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_units')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="edit_due_date" class="form-label">
                            Due Date
                        </label>
                        <input
                            id="edit_due_date"
                            type="date"
                            name="due_date"
                            class="form-input @error('due_date') form-input-error @enderror">
                        @error('due_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="edit_status" class="form-label">
                            Status
                        </label>
                        <select
                            id="edit_status"
                            name="status"
                            class="form-input @error('status') form-input-error @enderror">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('editModal')" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content delete-modal-content">
            <div class="delete-icon">⚠️</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Project?</h3>
            <p class="text-gray-500 mb-6">This action cannot be undone. Are you sure you want to delete this project?</p>
            
            <form id="deleteForm" action="" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" style="background-color: #dc2626; color: white; padding: 0.625rem 1.5rem; border-radius: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: opacity 0.15s ease;">
                    Delete Project
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Store the edit project ID when there are errors
    @if($errors->any() && session('edit_error'))
        // Keep the edit modal open with the error data
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            // Set the form action with the project ID from session
            const editForm = document.getElementById('editForm');
            if (editForm && '{{ session('edit_project_id') }}') {
                editForm.action = '/updateProject/{{ session('edit_project_id') }}';
            }
            
            // Populate old values from session flash data
            @if(old('project_name'))
                document.getElementById('edit_project_name').value = '{{ old('project_name') }}';
            @endif
            @if(old('project_manager'))
                document.getElementById('edit_project_manager').value = '{{ old('project_manager') }}';
            @endif
            @if(old('location_address'))
                document.getElementById('edit_location_address').value = '{{ old('location_address') }}';
            @endif
            @if(old('total_floors'))
                document.getElementById('edit_total_floors').value = '{{ old('total_floors') }}';
            @endif
            @if(old('completed_floors'))
                document.getElementById('edit_completed_floors').value = '{{ old('completed_floors') }}';
            @endif
            @if(old('total_units'))
                document.getElementById('edit_total_units').value = '{{ old('total_units') }}';
            @endif
            @if(old('due_date'))
                document.getElementById('edit_due_date').value = '{{ old('due_date') }}';
            @endif
            @if(old('status'))
                document.getElementById('edit_status').value = '{{ old('status') }}';
            @endif
        });
    @endif

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });

    // Open edit modal with project data
    function openEditModal(projectId) {
        // Store project ID for error handling
        window.editProjectId = projectId;
        
        // Fetch project data via AJAX
        fetch(`/projects/${projectId}/edit`)
            .then(response => response.json())
            .then(project => {
                // Set form action
                document.getElementById('editForm').action = `/updateProject/${projectId}`;
                
                // Populate form fields
                document.getElementById('edit_project_name').value = project.project_name || '';
                document.getElementById('edit_project_manager').value = project.project_manager || '';
                document.getElementById('edit_location_address').value = project.location_address || '';
                document.getElementById('edit_total_floors').value = project.total_floors || 0;
                document.getElementById('edit_completed_floors').value = project.completed_floors || 0;
                document.getElementById('edit_total_units').value = project.total_units || 0;
                document.getElementById('edit_due_date').value = project.due_date || '';
                document.getElementById('edit_status').value = project.status || 'active';
                
                // Open modal
                openModal('editModal');
            })
            .catch(error => {
                console.error('Error fetching project data:', error);
                // Show error toast
                showToast('Error loading project data', 'error');
            });
    }

    // Confirm delete
    function confirmDelete(projectId) {
        document.getElementById('deleteForm').action = `/updateProject/${projectId}`;
        openModal('deleteModal');
    }

    // Toast notification helper
    function showToast(message, type = 'success') {
        const existingToast = document.getElementById('toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = `toast toast-${type} show`;
        toast.innerHTML = `<div class="toast-message">${message}</div>`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }

    // Auto-hide toast after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 5000);
        }
    });
</script>

@endsection