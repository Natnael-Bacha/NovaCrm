@extends('layouts.app')

@section('title', 'Projects · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    /* Modal overlay & content – keeps the original behaviour */
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
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0F286F]">Projects</h1>
            <p class="text-gray-500 mt-0.5 sm:mt-1 text-xs sm:text-sm">Manage your construction projects and track progress.</p>
        </div>
        <button onclick="openModal('createModal')"
                class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 shadow-md text-sm sm:text-base">
            <span>+</span> New Project
        </button>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($projects as $project)
        <div class="group transition-all duration-200 ease-in-out border border-gray-200 rounded-xl p-4 sm:p-6 bg-white relative hover:-translate-y-0.5 hover:shadow-lg hover:border-[#0F286F]">
            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 flex gap-1 sm:gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <button onclick="openEditModal({{ $project->id }})"
                        class="bg-white border border-gray-200 rounded-lg px-1.5 py-0.5 sm:px-2 sm:py-1 text-[10px] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-[#0F286F] hover:text-white hover:border-[#0F286F]">✎ Edit</button>
                <button onclick="confirmDelete({{ $project->id }})"
                        class="bg-white border border-gray-200 rounded-lg px-1.5 py-0.5 sm:px-2 sm:py-1 text-[10px] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-red-600 hover:text-white hover:border-red-600">✕</button>
            </div>

            <div class="flex justify-between items-start mb-2 sm:mb-3">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 break-words pr-12">{{ $project->project_name }}</h3>
                {{-- Status removed since no status column exists --}}
            </div>

            <p class="text-xs sm:text-sm text-gray-600 mb-2 break-words">{{ $project->location_address ?? 'N/A' }}</p>

            <div class="flex items-center gap-4 text-xs sm:text-sm text-gray-500 mt-2 sm:mt-3">
                <span>Manager: {{ $project->project_manager ?? 'Not assigned' }}</span>
            </div>

            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100">
                <div class="flex justify-between text-xs sm:text-sm">
                    <span class="text-gray-600">Progress</span>
                    <span class="font-medium text-[#0F286F]">
                        {{ $project->total_floors ? round(($project->completed_floors ?? 0) / $project->total_floors * 100) : 0 }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2 mt-1.5 sm:mt-2 overflow-hidden">
                    <div class="h-full bg-[#0F286F] rounded-full transition-all duration-500" style="width: {{ $project->total_floors ? round(($project->completed_floors ?? 0) / $project->total_floors * 100) : 0 }}%;"></div>
                </div>
                <div class="flex justify-between text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1">
                    <span>{{ $project->completed_floors ?? 0 }} floors completed</span>
                    <span>{{ $project->total_floors ?? 0 }} total floors</span>
                </div>
            </div>

            <div class="flex justify-between items-center mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100 text-xs sm:text-sm">
                <span class="text-gray-400">Due: {{ $project->due_date ?? 'N/A' }}</span>
                <span class="text-gray-400">Units: {{ $project->total_units ?? 0 }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 sm:py-12">
            <p class="text-gray-400 text-base sm:text-lg">No projects found</p>
            <p class="text-gray-400 text-xs sm:text-sm mt-1">Click "New Project" to create your first project</p>
        </div>
        @endforelse

    </div>

    <!-- PAGINATION LINKS -->
     {{ $projects->links('vendor.pagination.custom') }}

    <!-- Create Project Modal -->
    <div id="createModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 @if($errors->any() && !session('edit_error')) active @endif">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-lg md:max-w-3xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#0F286F]">Create New Project</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Enter project details below</p>
                </div>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">✕</button>
            </div>

            <form action="{{ route('createProject') }}" method="POST" class="space-y-4 sm:space-y-6" id="createForm">
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

                    <!-- Project Name -->
                    <div>
                        <label for="project_name" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="project_name"
                            type="text"
                            name="project_name"
                            value="{{ old('project_name') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_name') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Enter project name"
                            required>
                        @error('project_name')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Project Manager -->
                    <div>
                        <label for="project_manager" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project Manager
                        </label>
                        <input
                            id="project_manager"
                            type="text"
                            name="project_manager"
                            value="{{ old('project_manager') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_manager') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Manager name">
                        @error('project_manager')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location Address -->
                    <div class="sm:col-span-2">
                        <label for="location_address" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Location Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="location_address"
                            type="text"
                            name="location_address"
                            value="{{ old('location_address') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('location_address') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Project location"
                            required>
                        @error('location_address')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Floors -->
                    <div>
                        <label for="total_floors" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Total Floors <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="total_floors"
                            type="number"
                            name="total_floors"
                            value="{{ old('total_floors') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('total_floors') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_floors')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Completed Floors -->
                    <div>
                        <label for="completed_floors" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Completed Floors
                        </label>
                        <input
                            id="completed_floors"
                            type="number"
                            name="completed_floors"
                            value="{{ old('completed_floors', 0) }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('completed_floors') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0">
                        @error('completed_floors')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Units -->
                    <div>
                        <label for="total_units" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Total Units <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="total_units"
                            type="number"
                            name="total_units"
                            value="{{ old('total_units') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('total_units') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_units')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Due Date
                        </label>
                        <input
                            id="due_date"
                            type="date"
                            name="due_date"
                            value="{{ old('due_date') }}"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('due_date') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror">
                        @error('due_date')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status field removed --}}

                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('createModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all border-none cursor-pointer font-semibold text-sm sm:text-base">
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 @if($errors->any() && session('edit_error')) active @endif">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-lg md:max-w-3xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#0F286F]">Edit Project</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Update project details</p>
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

                    <!-- Project Name -->
                    <div>
                        <label for="edit_project_name" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_project_name"
                            type="text"
                            name="project_name"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_name') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Enter project name"
                            required>
                        @error('project_name')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Project Manager -->
                    <div>
                        <label for="edit_project_manager" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Project Manager
                        </label>
                        <input
                            id="edit_project_manager"
                            type="text"
                            name="project_manager"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('project_manager') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Manager name">
                        @error('project_manager')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location Address -->
                    <div class="sm:col-span-2">
                        <label for="edit_location_address" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Location Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_location_address"
                            type="text"
                            name="location_address"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('location_address') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="Project location"
                            required>
                        @error('location_address')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Floors -->
                    <div>
                        <label for="edit_total_floors" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Total Floors <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_total_floors"
                            type="number"
                            name="total_floors"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('total_floors') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_floors')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Completed Floors -->
                    <div>
                        <label for="edit_completed_floors" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Completed Floors
                        </label>
                        <input
                            id="edit_completed_floors"
                            type="number"
                            name="completed_floors"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('completed_floors') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0">
                        @error('completed_floors')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Units -->
                    <div>
                        <label for="edit_total_units" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Total Units <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit_total_units"
                            type="number"
                            name="total_units"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('total_units') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror"
                            placeholder="0"
                            min="0"
                            required>
                        @error('total_units')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="edit_due_date" class="block mb-1.5 sm:mb-2 font-semibold text-[#0F286F] text-sm sm:text-base">
                            Due Date
                        </label>
                        <input
                            id="edit_due_date"
                            type="date"
                            name="due_date"
                            class="w-full border border-gray-300 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:border-[#0F286F] focus:ring-2 focus:ring-[#0F286F]/10 outline-none transition @error('due_date') border-red-600 focus:border-red-600 focus:ring-red-600/10 @enderror">
                        @error('due_date')
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status field removed --}}

                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('editModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-[#0F286F] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl hover:opacity-90 hover:-translate-y-0.5 transition-all border-none cursor-pointer font-semibold text-sm sm:text-base">
                        Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-md max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-5 sm:p-8 shadow-2xl text-center">
            <div class="text-4xl sm:text-5xl text-red-600 mb-3 sm:mb-4">⚠️</div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Delete Project?</h3>
            <p class="text-sm sm:text-base text-gray-500 mb-5 sm:mb-6">This action cannot be undone. Are you sure you want to delete this project?</p>

            <form id="deleteForm" action="" method="POST" class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:opacity-90 transition border-none cursor-pointer text-sm sm:text-base">
                    Delete Project
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Store the edit project ID when there are errors
    @if($errors->any() && session('edit_error'))
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            const editForm = document.getElementById('editForm');
            if (editForm && '{{ session('edit_project_id') }}') {
                editForm.action = '/updateProject/{{ session('edit_project_id') }}';
            }

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
        window.editProjectId = projectId;

        fetch(`/projects/${projectId}/edit`)
            .then(response => response.json())
            .then(project => {
                document.getElementById('editForm').action = `/updateProject/${projectId}`;
                document.getElementById('edit_project_name').value = project.project_name || '';
                document.getElementById('edit_project_manager').value = project.project_manager || '';
                document.getElementById('edit_location_address').value = project.location_address || '';
                document.getElementById('edit_total_floors').value = project.total_floors || 0;
                document.getElementById('edit_completed_floors').value = project.completed_floors || 0;
                document.getElementById('edit_total_units').value = project.total_units || 0;
                document.getElementById('edit_due_date').value = project.due_date || '';
                // Status field removed from modal, so no assignment needed
                openModal('editModal');
            })
            .catch(error => {
                console.error('Error fetching project data:', error);
                showToast('Error loading project data', 'error');
            });
    }

    // Confirm delete
    function confirmDelete(projectId) {
        document.getElementById('deleteForm').action = `/deleteProject/${projectId}`;
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
    });
</script>

@endsection