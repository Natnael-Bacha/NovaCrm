@extends('layouts.app')

@section('title', 'Units · NovaTra')

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
    
    .unit-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        background: white;
        position: relative;
    }
    
    .unit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 40, 111, 0.08);
        border-color: #0F286F;
    }
    
    .unit-card-actions {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .unit-card:hover .unit-card-actions {
        opacity: 1;
    }
    
    .unit-card-actions button {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #4b5563;
    }
    
    .unit-card-actions button:hover {
        background: #0F286F;
        color: white;
        border-color: #0F286F;
    }
    
    .unit-card-actions .edit-btn:hover {
        background: #0F286F;
    }
    
    .unit-card-actions .delete-btn:hover {
        background: #dc2626;
        border-color: #dc2626;
    }
    
    .status-text {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.025em;
    }
    
    .status-available {
        color: #065f46;
    }
    
    .status-reserved {
        color: #92400e;
    }
    
    .status-sold {
        color: #991b1b;
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
        background: white;
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

    .unit-type-text {
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #1e40af;
    }

    .unit-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0F286F;
    }

    /* Filter styles */
    .filter-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .filter-select {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        background: white;
        transition: all 0.15s ease;
        outline: none;
        color: #1e293b;
    }

    .filter-select:focus {
        border-color: #0F286F;
        box-shadow: 0 0 0 3px rgba(15, 40, 111, 0.1);
    }

    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #0F286F;
        margin-bottom: 0.375rem;
    }

    .clear-filter-btn {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
        padding: 0.625rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .clear-filter-btn:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #f1f4f9;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        color: #1e293b;
    }

    .filter-badge .remove {
        cursor: pointer;
        color: #6b7280;
        font-weight: 700;
    }

    .filter-badge .remove:hover {
        color: #dc2626;
    }

    /* No results */
    .no-results {
        text-align: center;
        padding: 3rem 0;
    }

    .no-results-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .no-results-text {
        color: #6b7280;
        font-size: 1.125rem;
    }

    .no-results-sub {
        color: #9ca3af;
        font-size: 0.875rem;
        margin-top: 0.25rem;
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
            <h1 class="text-3xl font-bold" style="color: #0F286F;">Units</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage units across all your projects.</p>
        </div>
        <button onclick="openModal('createModal')" 
                class="text-white px-6 py-3 rounded-xl hover:opacity-90 transition flex items-center gap-2 shadow-md"
                style="background-color: #0F286F;">
            <span>+</span> New Unit
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <div class="stat-value">{{ $units->count() }}</div>
            <div class="stat-label">Total Units</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $units->where('status', 'available')->count() }}</div>
            <div class="stat-label">Available</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $units->where('status', 'reserved')->count() }}</div>
            <div class="stat-label">Reserved</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $units->where('status', 'sold')->count() }}</div>
            <div class="stat-label">Sold</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-container">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_project" class="filter-label">Filter by Project</label>
                <select id="filter_project" class="filter-select" onchange="updateFloorFilterOptions(); applyFilters();">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="filter_floor" class="filter-label">Filter by Floor</label>
                <select id="filter_floor" class="filter-select" onchange="applyFilters()">
                    <option value="">All Floors</option>
                </select>
            </div>
            
            <div>
                <label for="filter_status" class="filter-label">Filter by Status</label>
                <select id="filter_status" class="filter-select" onchange="applyFilters()">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button onclick="clearFilters()" class="clear-filter-btn w-full">
                    Clear Filters
                </button>
            </div>
        </div>
        
        <!-- Active Filters Display -->
        <div id="activeFilters" class="flex flex-wrap gap-2 mt-4" style="display: none;">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>

    <!-- Units Grid -->
    <div id="unitsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($units as $unit)
        <div class="unit-card" data-unit-id="{{ $unit->id }}" data-project-id="{{ $unit->project_id }}" data-floor="{{ $unit->floor }}" data-status="{{ $unit->status }}">
            <div class="unit-card-actions">
                <button onclick="openEditModal({{ $unit->id }})" class="edit-btn">✎ Edit</button>
                <button onclick="confirmDelete({{ $unit->id }})" class="delete-btn">✕</button>
            </div>
            
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Unit #{{ $unit->unit_number }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $unit->project->project_name ?? 'No Project Assigned' }}
                    </p>
                </div>
                <span class="status-text 
                    @if($unit->status == 'available') status-available
                    @elseif($unit->status == 'reserved') status-reserved
                    @else status-sold @endif">
                    {{ ucfirst($unit->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <p class="text-xs text-gray-400">Floor</p>
                    <p class="text-sm font-medium text-gray-700">{{ $unit->floor }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Type</p>
                    <span class="unit-type-text">{{ ucfirst(str_replace('_', ' ', $unit->unit_type)) }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-1">
                <div>
                    <p class="text-xs text-gray-400">Size</p>
                    <p class="text-sm font-medium text-gray-700">{{ number_format($unit->size, 2) }} sq ft</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Price</p>
                    <p class="unit-price">ETB {{ number_format($unit->price, 2) }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-400 text-lg">No units found</p>
            <p class="text-gray-400 text-sm mt-1">Click "New Unit" to create your first unit</p>
        </div>
        @endforelse
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="no-results" style="display: none;">
        <div class="no-results-icon">🔍</div>
        <p class="no-results-text">No units match your filters</p>
        <p class="no-results-sub">Try adjusting your filter criteria</p>
    </div>

    <!-- Create Unit Modal -->
    <div id="createModal" class="modal-overlay @if($errors->any() && !session('edit_error')) active @endif">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold" style="color: #0F286F;">Create New Unit</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Enter unit details below</p>
                </div>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form action="{{ route('createUnit') }}" method="POST" class="space-y-6" id="createForm">
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

                    <!-- Project -->
                    <div>
                        <label for="project_id" class="form-label">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="project_id"
                            name="project_id"
                            class="form-input @error('project_id') form-input-error @enderror"
                            required
                            onchange="updateFloorOptions('create')">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-total-floors="{{ $project->total_floors }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }} ({{ $project->total_floors }} floors)
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Number -->
                    <div>
                        <label for="unit_number" class="form-label">
                            Unit Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="unit_number"
                            type="text"
                            name="unit_number"
                            value="{{ old('unit_number') }}"
                            class="form-input @error('unit_number') form-input-error @enderror"
                            placeholder="e.g., A101"
                            required
                            maxlength="255">
                        @error('unit_number')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Floor (Dropdown) -->
                    <div>
                        <label for="floor" class="form-label">
                            Floor <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="floor"
                            name="floor"
                            class="form-input @error('floor') form-input-error @enderror"
                            required>
                            <option value="">Select Floor</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        @error('floor')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label for="unit_type" class="form-label">
                            Unit Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="unit_type"
                            name="unit_type"
                            class="form-input @error('unit_type') form-input-error @enderror"
                            required>
                            <option value="">Select Type</option>
                            <option value="apartment" {{ old('unit_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="penthouse" {{ old('unit_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                            <option value="office_space" {{ old('unit_type') == 'office_space' ? 'selected' : '' }}>Office Space</option>
                            <option value="commercial" {{ old('unit_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="studio" {{ old('unit_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="duplex" {{ old('unit_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                        </select>
                        @error('unit_type')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="size" class="form-label">
                            Size (sq ft) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="size"
                            type="number"
                            step="0.01"
                            name="size"
                            value="{{ old('size') }}"
                            class="form-input @error('size') form-input-error @enderror"
                            placeholder="e.g., 1200"
                            required>
                        @error('size')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="form-label">
                            Price (ETB) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="price"
                            type="number"
                            step="0.01"
                            name="price"
                            value="{{ old('price') }}"
                            class="form-input @error('price') form-input-error @enderror"
                            placeholder="e.g., 250000"
                            required>
                        @error('price')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="form-label">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="form-input @error('status') form-input-error @enderror"
                            required>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
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
                        Create Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div id="editModal" class="modal-overlay @if($errors->any() && session('edit_error')) active @endif">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold" style="color: #0F286F;">Edit Unit</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Update unit details</p>
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

                    <!-- Project -->
                    <div>
                        <label for="edit_project_id" class="form-label">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_project_id"
                            name="project_id"
                            class="form-input @error('project_id') form-input-error @enderror"
                            required
                            onchange="updateFloorOptions('edit')">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-total-floors="{{ $project->total_floors }}">
                                    {{ $project->project_name }} ({{ $project->total_floors }} floors)
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Number -->
                    <div>
                        <label for="edit_unit_number" class="form-label">
                            Unit Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_unit_number"
                            type="text"
                            name="unit_number"
                            class="form-input @error('unit_number') form-input-error @enderror"
                            placeholder="e.g., A101"
                            required
                            maxlength="255">
                        @error('unit_number')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Floor (Dropdown) -->
                    <div>
                        <label for="edit_floor" class="form-label">
                            Floor <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_floor"
                            name="floor"
                            class="form-input @error('floor') form-input-error @enderror"
                            required>
                            <option value="">Select Floor</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        @error('floor')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label for="edit_unit_type" class="form-label">
                            Unit Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_unit_type"
                            name="unit_type"
                            class="form-input @error('unit_type') form-input-error @enderror"
                            required>
                            <option value="apartment">Apartment</option>
                            <option value="penthouse">Penthouse</option>
                            <option value="office_space">Office Space</option>
                            <option value="commercial">Commercial</option>
                            <option value="studio">Studio</option>
                            <option value="duplex">Duplex</option>
                        </select>
                        @error('unit_type')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="edit_size" class="form-label">
                            Size (sq ft) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_size"
                            type="number"
                            step="0.01"
                            name="size"
                            class="form-input @error('size') form-input-error @enderror"
                            placeholder="e.g., 1200"
                            required>
                        @error('size')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="edit_price" class="form-label">
                            Price (ETB) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_price"
                            type="number"
                            step="0.01"
                            name="price"
                            class="form-input @error('price') form-input-error @enderror"
                            placeholder="e.g., 250000"
                            required>
                        @error('price')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="edit_status" class="form-label">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_status"
                            name="status"
                            class="form-input @error('status') form-input-error @enderror"
                            required>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="sold">Sold</option>
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
                        Update Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content delete-modal-content">
            <div class="delete-icon">⚠️</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Unit?</h3>
            <p class="text-gray-500 mb-6">This action cannot be undone. Are you sure you want to delete this unit?</p>
            
            <form id="deleteForm" action="" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" style="background-color: #dc2626; color: white; padding: 0.625rem 1.5rem; border-radius: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: opacity 0.15s ease;">
                    Delete Unit
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Store all units data from the server
    const unitsData = @json($units);
    const projectsData = @json($projects);

    // Function to update floor dropdown options in create/edit modals
    function updateFloorOptions(mode = 'create') {
        const prefix = mode === 'create' ? '' : 'edit_';
        const projectSelect = document.getElementById(`${prefix}project_id`);
        const floorSelect = document.getElementById(`${prefix}floor`);
        
        // Get the selected option
        const selectedOption = projectSelect.options[projectSelect.selectedIndex];
        
        // Clear existing options
        floorSelect.innerHTML = '<option value="">Select Floor</option>';
        
        if (selectedOption && selectedOption.value) {
            // Get total floors from data attribute
            const totalFloors = parseInt(selectedOption.getAttribute('data-total-floors')) || 0;
            
            if (totalFloors > 0) {
                for (let i = 1; i <= totalFloors; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `Floor ${i}`;
                    floorSelect.appendChild(option);
                }
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No floors available';
                option.disabled = true;
                floorSelect.appendChild(option);
            }
        }
        
        // If editing, select the current floor value
        if (mode === 'edit') {
            const currentFloorInput = document.getElementById('edit_floor_current');
            if (currentFloorInput && currentFloorInput.value) {
                floorSelect.value = currentFloorInput.value;
            }
        }
    }

    // Function to update floor filter options based on selected project
    function updateFloorFilterOptions() {
        const projectFilter = document.getElementById('filter_project');
        const floorFilter = document.getElementById('filter_floor');
        const selectedProjectId = projectFilter.value;
        
        // Clear existing options
        floorFilter.innerHTML = '<option value="">All Floors</option>';
        
        if (selectedProjectId) {
            // Get unique floors from unitsData
            const floors = new Set();
            unitsData.forEach(unit => {
                if (unit.project_id == selectedProjectId) {
                    floors.add(unit.floor);
                }
            });
            
            // Sort floors
            const sortedFloors = Array.from(floors).sort((a, b) => a - b);
            
            if (sortedFloors.length > 0) {
                sortedFloors.forEach(floor => {
                    const option = document.createElement('option');
                    option.value = floor;
                    option.textContent = `Floor ${floor}`;
                    floorFilter.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No units on any floor';
                option.disabled = true;
                floorFilter.appendChild(option);
            }
        }
        
        // Reset floor filter value if it's not in the new options
        const currentFloorValue = floorFilter.value;
        let valueExists = false;
        for (let i = 0; i < floorFilter.options.length; i++) {
            if (floorFilter.options[i].value == currentFloorValue) {
                valueExists = true;
                break;
            }
        }
        if (!valueExists) {
            floorFilter.value = '';
        }
    }

    // Apply filters
    function applyFilters() {
        const projectFilter = document.getElementById('filter_project');
        const floorFilter = document.getElementById('filter_floor');
        const statusFilter = document.getElementById('filter_status');
        const selectedProject = projectFilter.value;
        const selectedFloor = floorFilter.value;
        const selectedStatus = statusFilter.value;
        
        const cards = document.querySelectorAll('.unit-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const cardProject = card.dataset.projectId;
            const cardFloor = parseInt(card.dataset.floor);
            const cardStatus = card.dataset.status;
            
            let show = true;
            
            // Filter by project
            if (selectedProject && cardProject != selectedProject) {
                show = false;
            }
            
            // Filter by floor
            if (selectedFloor && show) {
                if (cardFloor !== parseInt(selectedFloor)) {
                    show = false;
                }
            }
            
            // Filter by status
            if (selectedStatus && show) {
                if (cardStatus !== selectedStatus) {
                    show = false;
                }
            }
            
            card.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        
        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0 && cards.length > 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
        
        // Update active filters display
        updateActiveFilters(selectedProject, selectedFloor, selectedStatus);
        
        // Update URL with filter params
        updateURLParams(selectedProject, selectedFloor, selectedStatus);
    }

    // Update active filters display
    function updateActiveFilters(projectId, floor, status) {
        const container = document.getElementById('activeFilters');
        container.innerHTML = '';
        
        let hasFilters = false;
        
        if (projectId) {
            const project = projectsData.find(p => p.id == projectId);
            if (project) {
                hasFilters = true;
                const badge = document.createElement('span');
                badge.className = 'filter-badge';
                badge.innerHTML = `
                    Project: ${project.project_name}
                    <span class="remove" onclick="removeFilter('project')">×</span>
                `;
                container.appendChild(badge);
            }
        }
        
        if (floor) {
            hasFilters = true;
            const badge = document.createElement('span');
            badge.className = 'filter-badge';
            badge.innerHTML = `
                Floor: ${floor}
                <span class="remove" onclick="removeFilter('floor')">×</span>
            `;
            container.appendChild(badge);
        }
        
        if (status) {
            hasFilters = true;
            const statusLabels = {
                'available': 'Available',
                'reserved': 'Reserved',
                'sold': 'Sold'
            };
            const badge = document.createElement('span');
            badge.className = 'filter-badge';
            badge.innerHTML = `
                Status: ${statusLabels[status] || status}
                <span class="remove" onclick="removeFilter('status')">×</span>
            `;
            container.appendChild(badge);
        }
        
        container.style.display = hasFilters ? 'flex' : 'none';
    }

    // Remove a specific filter
    function removeFilter(type) {
        if (type === 'project') {
            document.getElementById('filter_project').value = '';
            updateFloorFilterOptions();
        } else if (type === 'floor') {
            document.getElementById('filter_floor').value = '';
        } else if (type === 'status') {
            document.getElementById('filter_status').value = '';
        }
        applyFilters();
    }

    // Clear all filters
    function clearFilters() {
        document.getElementById('filter_project').value = '';
        document.getElementById('filter_floor').value = '';
        document.getElementById('filter_status').value = '';
        updateFloorFilterOptions();
        applyFilters();
    }

    // Update URL parameters
    function updateURLParams(projectId, floor, status) {
        const url = new URL(window.location);
        if (projectId) {
            url.searchParams.set('project', projectId);
        } else {
            url.searchParams.delete('project');
        }
        if (floor) {
            url.searchParams.set('floor', floor);
        } else {
            url.searchParams.delete('floor');
        }
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        window.history.replaceState({}, '', url);
    }

    // Open edit modal with unit data (using unitsData from server)
    function openEditModal(unitId) {
        // Find the unit in the unitsData array
        const unit = unitsData.find(u => u.id === unitId);
        
        if (!unit) {
            showToast('Unit not found', 'error');
            return;
        }
        
        // Set form action
        document.getElementById('editForm').action = `/updateUnit/${unitId}`;
        
        // Populate form fields
        document.getElementById('edit_project_id').value = unit.project_id || '';
        document.getElementById('edit_unit_number').value = unit.unit_number || '';
        document.getElementById('edit_unit_type').value = unit.unit_type || '';
        document.getElementById('edit_size').value = unit.size || 0;
        document.getElementById('edit_price').value = unit.price || 0;
        document.getElementById('edit_status').value = unit.status || 'available';
        
        // Store current floor for selection after options are populated
        const currentFloor = unit.floor || '';
        // Remove existing hidden input if any
        const existingHidden = document.getElementById('edit_floor_current');
        if (existingHidden) {
            existingHidden.remove();
        }
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = 'edit_floor_current';
        hiddenInput.value = currentFloor;
        document.getElementById('editForm').appendChild(hiddenInput);
        
        // Update floor options based on selected project
        updateFloorOptions('edit');
        
        // Open modal
        openModal('editModal');
    }

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

    // Confirm delete
    function confirmDelete(unitId) {
        // Set the action URL with the unit ID
        document.getElementById('deleteForm').action = `/deleteUnit/${unitId}`;
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
        
        // Initialize floor filter options
        updateFloorFilterOptions();
        
        // Apply any existing filters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const projectParam = urlParams.get('project');
        const floorParam = urlParams.get('floor');
        const statusParam = urlParams.get('status');
        
        if (projectParam) {
            document.getElementById('filter_project').value = projectParam;
            updateFloorFilterOptions();
        }
        if (floorParam) {
            document.getElementById('filter_floor').value = floorParam;
        }
        if (statusParam) {
            document.getElementById('filter_status').value = statusParam;
        }
        
        // Apply filters after a small delay
        setTimeout(applyFilters, 100);
        
        // For create modal - check if there's a selected project
        const createProjectSelect = document.getElementById('project_id');
        if (createProjectSelect && createProjectSelect.value) {
            updateFloorOptions('create');
        }
    });

    // Store the edit unit ID when there are errors
    @if($errors->any() && session('edit_error'))
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            const editForm = document.getElementById('editForm');
            if (editForm && '{{ session('edit_unit_id') }}') {
                editForm.action = '/updateUnit/{{ session('edit_unit_id') }}';
            }
            
            @if(old('project_id'))
                document.getElementById('edit_project_id').value = '{{ old('project_id') }}';
            @endif
            @if(old('unit_number'))
                document.getElementById('edit_unit_number').value = '{{ old('unit_number') }}';
            @endif
            @if(old('floor'))
                const hiddenFloor = document.createElement('input');
                hiddenFloor.type = 'hidden';
                hiddenFloor.id = 'edit_floor_current';
                hiddenFloor.value = '{{ old('floor') }}';
                document.getElementById('editForm').appendChild(hiddenFloor);
                
                setTimeout(function() {
                    updateFloorOptions('edit');
                }, 100);
            @endif
            @if(old('unit_type'))
                document.getElementById('edit_unit_type').value = '{{ old('unit_type') }}';
            @endif
            @if(old('size'))
                document.getElementById('edit_size').value = '{{ old('size') }}';
            @endif
            @if(old('price'))
                document.getElementById('edit_price').value = '{{ old('price') }}';
            @endif
            @if(old('status'))
                document.getElementById('edit_status').value = '{{ old('status') }}';
            @endif
        });
    @endif
</script>

@endsection