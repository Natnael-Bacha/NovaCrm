@extends('layouts.app')

@section('title', 'Units · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    /* Minimal CSS needed for modal overlay transitions – Tailwind can't handle the 'active' toggle without JS, we keep these classes */
    .modal-overlay {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .modal-overlay.active {
        opacity: 1;
        pointer-events: all;
    }
    .modal-content {
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }
    .modal-overlay.active .modal-content {
        transform: scale(1);
    }
    /* Extra small helper for unit-type */
    .unit-type-text {
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #1e40af;
    }
</style>
@endpush

@section('content')

<div class="p-4 sm:p-6 md:p-10">

    <!-- Toast Notification -->
    @if(session('success'))
    <div id="toast" class="fixed top-4 right-4 sm:top-8 sm:right-8 z-[9999] px-4 py-3 sm:px-6 sm:py-4 rounded-xl bg-white shadow-xl border-l-4 border-green-500 transform translate-x-0 transition-transform duration-300 max-w-[calc(100%-2rem)] sm:max-w-sm">
        <div class="text-slate-800 text-sm">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div id="toast" class="fixed top-4 right-4 sm:top-8 sm:right-8 z-[9999] px-4 py-3 sm:px-6 sm:py-4 rounded-xl bg-white shadow-xl border-l-4 border-red-500 transform translate-x-0 transition-transform duration-300 max-w-[calc(100%-2rem)] sm:max-w-sm">
        <div class="text-slate-800 text-sm">{{ session('error') }}</div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row flex-wrap justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0F286F]">Units</h1>
            <p class="text-gray-500 mt-0.5 sm:mt-1 text-xs sm:text-sm">Manage units across all your projects.</p>
        </div>
        <button onclick="openModal('createModal')"
                class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 shadow-md text-sm sm:text-base">
            <span>+</span> New Unit
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 mb-4 sm:mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_project" class="block text-sm font-semibold text-[#0F286F] mb-1.5">Filter by Project</label>
                <select id="filter_project" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition text-sm" onchange="updateFloorFilterOptions(); applyFilters();">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter_floor" class="block text-sm font-semibold text-[#0F286F] mb-1.5">Filter by Floor</label>
                <select id="filter_floor" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition text-sm" onchange="applyFilters()">
                    <option value="">All Floors</option>
                </select>
            </div>

            <div>
                <label for="filter_status" class="block text-sm font-semibold text-[#0F286F] mb-1.5">Filter by Status</label>
                <select id="filter_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition text-sm" onchange="applyFilters()">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>

            <div class="flex items-end">
                <button onclick="clearFilters()" class="w-full bg-transparent text-gray-600 border border-gray-300 px-4 py-2.5 rounded-xl font-medium hover:bg-gray-50 hover:border-gray-400 transition text-sm">
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
    <div id="unitsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($units as $unit)
        <div class="unit-card group transition-all duration-200 ease-in-out border border-gray-200 rounded-xl p-4 sm:p-6 bg-white relative hover:-translate-y-0.5 hover:shadow-lg hover:border-[#0F286F]"
             data-unit-id="{{ $unit->id }}" data-project-id="{{ $unit->project_id }}" data-floor="{{ $unit->floor }}" data-status="{{ $unit->status }}">
            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 flex gap-1 sm:gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <button onclick="openEditModal({{ $unit->id }})"
                        class="bg-white border border-gray-200 rounded-lg px-1.5 py-0.5 sm:px-2 sm:py-1 text-[10px] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-[#0F286F] hover:text-white hover:border-[#0F286F]">✎ Edit</button>
                <button onclick="confirmDelete({{ $unit->id }})"
                        class="bg-white border border-gray-200 rounded-lg px-1.5 py-0.5 sm:px-2 sm:py-1 text-[10px] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-red-600 hover:text-white hover:border-red-600">✕</button>
            </div>

            <div class="flex justify-between items-start mb-2 sm:mb-3">
                <div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 break-words pr-12">Unit #{{ $unit->unit_number }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500">
                        {{ $unit->project->project_name ?? 'No Project Assigned' }}
                    </p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide
                    @if($unit->status == 'available') text-green-700
                    @elseif($unit->status == 'reserved') text-yellow-700
                    @else text-red-700 @endif">
                    {{ ucfirst($unit->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <p class="text-[10px] sm:text-xs text-gray-400">Floor</p>
                    <p class="text-xs sm:text-sm font-medium text-gray-700">{{ $unit->floor }}</p>
                </div>
                <div>
                    <p class="text-[10px] sm:text-xs text-gray-400">Type</p>
                    <span class="unit-type-text">{{ ucfirst(str_replace('_', ' ', $unit->unit_type)) }}</span>
                </div>
            </div>

            <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-1">
                <div>
                    <p class="text-[10px] sm:text-xs text-gray-400">Size</p>
                    <p class="text-xs sm:text-sm font-medium text-gray-700">{{ number_format($unit->size, 2) }} sq ft</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] sm:text-xs text-gray-400">Price</p>
                    <p class="text-base sm:text-lg font-bold text-[#0F286F]">ETB {{ number_format($unit->price, 2) }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 sm:py-12">
            <p class="text-gray-400 text-base sm:text-lg">No units found</p>
            <p class="text-gray-400 text-xs sm:text-sm mt-1">Click "New Unit" to create your first unit</p>
        </div>
        @endforelse
    </div>

    <!-- PAGINATION LINKS -->
     {{ $units->links('vendor.pagination.custom') }}

    <!-- No Results Message -->
    <div id="noResults" class="text-center py-8 sm:py-12" style="display: none;">
        <div class="text-4xl sm:text-5xl text-gray-300 mb-3 sm:mb-4">🔍</div>
        <p class="text-gray-500 text-base sm:text-lg">No units match your filters</p>
        <p class="text-gray-400 text-xs sm:text-sm mt-1">Try adjusting your filter criteria</p>
    </div>

    <!-- Create Unit Modal -->
    <div id="createModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 @if($errors->any() && !session('edit_error')) active @endif">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-lg md:max-w-3xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#0F286F]">Create New Unit</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Enter unit details below</p>
                </div>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form action="{{ route('createUnit') }}" method="POST" class="space-y-4 sm:space-y-6" id="createForm">
                @csrf

                <!-- Error Summary -->
                @if($errors->any() && !session('edit_error'))
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 sm:p-4 mb-3 sm:mb-4 block">
                    <div class="text-red-600 font-semibold text-xs sm:text-sm mb-1">Please fix the following errors:</div>
                    <ul class="text-red-800 text-xs sm:text-sm list-disc pl-4 sm:pl-5 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">

                    <!-- Project -->
                    <div>
                        <label for="project_id" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="project_id"
                            name="project_id"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_id') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
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
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Number -->
                    <div>
                        <label for="unit_number" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Unit Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="unit_number"
                            type="text"
                            name="unit_number"
                            value="{{ old('unit_number') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('unit_number') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., A101"
                            required
                            maxlength="255">
                        @error('unit_number')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Floor (Dropdown) -->
                    <div>
                        <label for="floor" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Floor <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="floor"
                            name="floor"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('floor') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required>
                            <option value="">Select Floor</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        @error('floor')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label for="unit_type" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Unit Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="unit_type"
                            name="unit_type"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('unit_type') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
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
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="size" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Size (sq ft) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="size"
                            type="number"
                            step="0.01"
                            name="size"
                            value="{{ old('size') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('size') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., 1200"
                            required>
                        @error('size')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Price (ETB) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="price"
                            type="number"
                            step="0.01"
                            name="price"
                            value="{{ old('price') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('price') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., 250000"
                            required>
                        @error('price')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('status') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        </select>
                        @error('status')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('createModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all border-none cursor-pointer font-semibold text-sm sm:text-base">
                        Create Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div id="editModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 @if($errors->any() && session('edit_error')) active @endif">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-lg md:max-w-3xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#0F286F]">Edit Unit</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Update unit details</p>
                </div>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form id="editForm" action="" method="POST" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <!-- Error Summary -->
                @if($errors->any() && session('edit_error'))
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 sm:p-4 mb-3 sm:mb-4 block">
                    <div class="text-red-600 font-semibold text-xs sm:text-sm mb-1">Please fix the following errors:</div>
                    <ul class="text-red-800 text-xs sm:text-sm list-disc pl-4 sm:pl-5 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">

                    <!-- Project -->
                    <div>
                        <label for="edit_project_id" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_project_id"
                            name="project_id"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_id') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required
                            onchange="updateFloorOptions('edit')">
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-total-floors="{{ $project->total_floors }}">
                                    {{ $project->project_name }} ({{ $project->total_floors }} floors)
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Number -->
                    <div>
                        <label for="edit_unit_number" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Unit Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_unit_number"
                            type="text"
                            name="unit_number"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('unit_number') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., A101"
                            required
                            maxlength="255">
                        @error('unit_number')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Floor (Dropdown) -->
                    <div>
                        <label for="edit_floor" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Floor <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_floor"
                            name="floor"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('floor') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required>
                            <option value="">Select Floor</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        @error('floor')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label for="edit_unit_type" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Unit Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_unit_type"
                            name="unit_type"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('unit_type') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required>
                            <option value="apartment">Apartment</option>
                            <option value="penthouse">Penthouse</option>
                            <option value="office_space">Office Space</option>
                            <option value="commercial">Commercial</option>
                            <option value="studio">Studio</option>
                            <option value="duplex">Duplex</option>
                        </select>
                        @error('unit_type')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="edit_size" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Size (sq ft) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_size"
                            type="number"
                            step="0.01"
                            name="size"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('size') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., 1200"
                            required>
                        @error('size')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="edit_price" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Price (ETB) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_price"
                            type="number"
                            step="0.01"
                            name="price"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('price') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="e.g., 250000"
                            required>
                        @error('price')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="edit_status" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_status"
                            name="status"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('status') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            required>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="sold">Sold</option>
                        </select>
                        @error('status')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('editModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all border-none cursor-pointer font-semibold text-sm sm:text-base">
                        Update Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-md max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-5 sm:p-8 shadow-2xl text-center">
            <div class="text-4xl sm:text-5xl text-red-600 mb-3 sm:mb-4">⚠️</div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Delete Unit?</h3>
            <p class="text-sm sm:text-base text-gray-500 mb-5 sm:mb-6">This action cannot be undone. Are you sure you want to delete this unit?</p>

            <form id="deleteForm" action="" method="POST" class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:opacity-90 transition border-none cursor-pointer text-sm sm:text-base">
                    Delete Unit
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Store all units data from the server
    const unitsData = @json($units->items()); // Fixed: extract items from paginator
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
                badge.className = 'filter-badge inline-flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-full text-sm text-gray-700';
                badge.innerHTML = `
                    Project: ${project.project_name}
                    <span class="remove cursor-pointer text-gray-500 hover:text-red-600 font-bold" onclick="removeFilter('project')">×</span>
                `;
                container.appendChild(badge);
            }
        }

        if (floor) {
            hasFilters = true;
            const badge = document.createElement('span');
            badge.className = 'filter-badge inline-flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-full text-sm text-gray-700';
            badge.innerHTML = `
                Floor: ${floor}
                <span class="remove cursor-pointer text-gray-500 hover:text-red-600 font-bold" onclick="removeFilter('floor')">×</span>
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
            badge.className = 'filter-badge inline-flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-full text-sm text-gray-700';
            badge.innerHTML = `
                Status: ${statusLabels[status] || status}
                <span class="remove cursor-pointer text-gray-500 hover:text-red-600 font-bold" onclick="removeFilter('status')">×</span>
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
        toast.className = `fixed top-4 right-4 sm:top-8 sm:right-8 z-[9999] px-4 py-3 sm:px-6 sm:py-4 rounded-xl bg-white shadow-xl border-l-4 ${type === 'success' ? 'border-green-500' : 'border-red-500'} transform translate-x-0 transition-transform duration-300 max-w-[calc(100%-2rem)] sm:max-w-sm`;
        toast.innerHTML = `<div class="text-slate-800 text-sm">${message}</div>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-[120%]');
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
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-[120%]');
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