@extends('layouts.app')

@section('title', 'Pipeline · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    /* Override main overflow for this view */
    main {
        overflow: hidden !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    /* Modal overlay & content – kept for transitions */
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

    /* Custom animations for lead cards */
    .lead-card.slide-left {
        animation: slideLeft 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    .lead-card.slide-right {
        animation: slideRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes slideLeft {
        0% { opacity: 1; transform: translateX(0) scale(1); }
        100% { opacity: 0; transform: translateX(-30px) scale(0.95); }
    }
    @keyframes slideRight {
        0% { opacity: 1; transform: translateX(0) scale(1); }
        100% { opacity: 0; transform: translateX(30px) scale(0.95); }
    }
    .lead-card.entering {
        animation: enter 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes enter {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }

    /* Spinner for buttons */
    .spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Drag-over border for columns */
    .kanban-column.drag-over {
        background: #f8faff;
        border-color: #0F286F;
        border-style: dashed;
    }
    .lead-card.dragging {
        opacity: 0.4;
        transform: scale(0.95);
    }

    /* Scrollbar styling for column body */
    .kanban-column-body::-webkit-scrollbar {
        width: 4px;
    }
    .kanban-column-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .kanban-column-body::-webkit-scrollbar-thumb {
        background: #c1c7cd;
        border-radius: 10px;
    }

    /* Pulse animation for count updates */
    .kanban-column-count.updating {
        animation: pulse-count 0.5s ease;
    }
    @keyframes pulse-count {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }

    /* Prevent button text from wrapping */
    .lead-actions button {
        white-space: nowrap;
    }

    /* Pipeline container – fills the entire main content area */
    #pipeline-container {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        padding: 1.5rem;
        overflow: hidden;
        box-sizing: border-box;
    }

    .kanban-wrapper {
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    .kanban-column {
        max-height: 100%;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .kanban-column-body {
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }
</style>
@endpush

@section('content')

<div id="pipeline-container" class="w-full">

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
    <div class="flex flex-col sm:flex-row flex-wrap justify-between items-start sm:items-center gap-4 mb-4 sm:mb-6 flex-shrink-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0F286F]">Pipeline</h1>
            <p class="text-gray-500 mt-0.5 sm:mt-1 text-xs sm:text-sm">Track your leads through each stage of the sales process</p>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-wrapper flex-1 min-h-0 overflow-hidden">
        <div class="overflow-x-auto h-full pb-2 -mx-2">
            <div class="flex gap-3 sm:gap-5 p-2 h-full items-stretch kanban-board" id="kanbanBoard">
                @php
                    $stages = [
                        'new' => ['label' => 'New', 'db_value' => 'new'],
                        'contacted' => ['label' => 'Contacted', 'db_value' => 'contacted'],
                        'qualified' => ['label' => 'Qualified', 'db_value' => 'qualified'],
                        'site_visit' => ['label' => 'Site Visit', 'db_value' => 'site visit'],
                        'proposal_sent' => ['label' => 'Proposal Sent', 'db_value' => 'proposal sent'],
                        'initial_payment' => ['label' => 'Initial Payment', 'db_value' => 'initial payment'],
                        'completed' => ['label' => 'Completed', 'db_value' => 'completed'],
                        'lost' => ['label' => 'Lost', 'db_value' => 'lost'],
                    ];
                @endphp

                @foreach($stages as $stageKey => $stage)
                    @php
                        $stageLeads = $leads->where('current_stage', $stage['db_value']);
                        $hasLeads = $stageLeads->count() > 0;
                    @endphp
                    <div class="kanban-column min-w-[200px] sm:min-w-[250px] md:min-w-[280px] max-w-[220px] sm:max-w-[280px] flex-shrink-0 bg-white rounded-xl p-3 sm:p-4 border border-gray-200 flex flex-col transition-all" data-stage="{{ $stageKey }}" id="column-{{ $stageKey }}">
                        <div class="flex justify-between items-center mb-3 sm:mb-4 pb-2 sm:pb-3 border-b-2 border-[#0F286F] flex-shrink-0">
                            <div class="font-semibold text-xs sm:text-sm uppercase tracking-wider text-[#0F286F] flex items-center gap-2">
                                {{ $stage['label'] }}
                            </div>
                            <span class="kanban-column-count bg-gray-200 px-2 py-0.5 rounded-full text-xs font-semibold text-gray-600 transition-all {{ $hasLeads ? 'bg-[#0F286F] text-white' : '' }}" id="count-{{ $stageKey }}">
                                {{ $stageLeads->count() }}
                            </span>
                        </div>
                        <div class="kanban-column-body flex-1 overflow-y-auto pr-1 min-h-[100px]"
                             id="body-{{ $stageKey }}"
                             ondragover="event.preventDefault();"
                             ondrop="handleDrop(event, '{{ $stageKey }}')">
                            @forelse($stageLeads as $lead)
                            <div class="lead-card bg-white rounded-xl p-3 sm:p-4 mb-2 sm:mb-3 border border-gray-200 cursor-pointer relative shadow-sm border-l-4 border-[#0F286F] transition-all hover:-translate-y-0.5 hover:shadow-md hover:border-[#0F286F] last:mb-0"
                                 id="lead-{{ $lead->id }}"
                                 draggable="true"
                                 data-lead-id="{{ $lead->id }}"
                                 data-stage="{{ $lead->current_stage }}"
                                 ondragstart="handleDragStart(event, {{ $lead->id }})"
                                 onclick="showLeadDetails({{ $lead->id }})">
                                <div class="flex justify-between items-start mb-1.5 sm:mb-2">
                                    <span class="font-semibold text-gray-800 text-sm sm:text-base flex-1 mr-2">{{ $lead->full_name }}</span>
                                    <span class="text-[0.6rem] font-semibold uppercase px-2 py-0.5 rounded-full bg-[#0F286F] text-white flex-shrink-0">
                                        {{ ucfirst($lead->lead_type ?? 'Other') }}
                                    </span>
                                </div>
                                <div class="flex flex-col gap-0.5 text-xs sm:text-sm text-gray-500">
                                    @if($lead->email)
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-medium text-gray-600 min-w-[50px] flex-shrink-0">Email:</span>
                                        <span class="truncate">{{ $lead->email }}</span>
                                    </div>
                                    @endif
                                    @if($lead->phone)
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-medium text-gray-600 min-w-[50px] flex-shrink-0">Phone:</span>
                                        <span class="truncate">{{ $lead->phone }}</span>
                                    </div>
                                    @endif
                                    @if($lead->budget_range)
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-medium text-gray-600 min-w-[50px] flex-shrink-0">Budget:</span>
                                        <span class="truncate">{{ $lead->budget_range }}</span>
                                    </div>
                                    @endif
                                    @if($lead->preferred_location)
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-medium text-gray-600 min-w-[50px] flex-shrink-0">Location:</span>
                                        <span class="truncate">{{ $lead->preferred_location }}</span>
                                    </div>
                                    @endif
                                    @if($lead->lead_source)
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-medium text-gray-600 min-w-[50px] flex-shrink-0">Source:</span>
                                        <span class="truncate">{{ ucfirst($lead->lead_source) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="lead-actions flex justify-end gap-2 mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-100" onclick="event.stopPropagation();">
                                    {{-- Back button: show on all except 'new' --}}
                                    @if($stageKey != 'new')
                                    <button type="button" class="move-left bg-transparent border border-gray-200 rounded-lg px-2 sm:px-3 py-0.5 sm:py-1 text-[0.65rem] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-gray-100 hover:border-gray-400 active:scale-95" onclick="moveLead({{ $lead->id }}, 'left', this)">
                                        ← Back
                                    </button>
                                    @endif

                                    {{-- Next button: hide ONLY on 'lost' --}}
                                    @if($stageKey != 'lost')
                                    <button type="button" class="move-right bg-transparent border border-gray-200 rounded-lg px-2 sm:px-3 py-0.5 sm:py-1 text-[0.65rem] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-[#0F286F] hover:text-white hover:border-[#0F286F] active:scale-95" onclick="moveLead({{ $lead->id }}, 'right', this)">
                                        Next →
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="empty-column text-center py-6 sm:py-8 text-gray-400 text-xs sm:text-sm" id="empty-{{ $stageKey }}">
                                <div class="text-sm">No leads in this stage</div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Lead Detail Modal -->
    <div id="leadDetailModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4">
        <div class="modal-content bg-white rounded-2xl sm:rounded-3xl w-full max-w-[95%] sm:max-w-lg md:max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#0F286F]" id="leadDetailName">Lead Details</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5" id="leadDetailStage">Stage: New</p>
                </div>
                <button onclick="closeModal('leadDetailModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">×</button>
            </div>

            <div id="leadDetailContent" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Populated by JavaScript -->
            </div>

            <div class="flex justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-100 mt-4">
                <button onclick="closeModal('leadDetailModal')" class="w-full sm:w-auto bg-transparent text-[#0F286F] border-2 border-[#0F286F] px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium hover:bg-[#f8faff] transition cursor-pointer text-sm sm:text-base">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    // Store leads data – this is now mutable so we can keep it in sync
    let leadsData = @json($leads);

    // Stage order
    const stageOrder = ['new', 'contacted', 'qualified', 'site_visit', 'proposal_sent', 'initial_payment', 'completed', 'lost'];

    const stageLabels = {
        'new': 'New',
        'contacted': 'Contacted',
        'qualified': 'Qualified',
        'site_visit': 'Site Visit',
        'proposal_sent': 'Proposal Sent',
        'initial_payment': 'Initial Payment',
        'completed': 'Completed',
        'lost': 'Lost'
    };

    const dbToKeyMap = {
        'new': 'new',
        'contacted': 'contacted',
        'qualified': 'qualified',
        'site visit': 'site_visit',
        'proposal sent': 'proposal_sent',
        'initial payment': 'initial_payment',
        'completed': 'completed',
        'lost': 'lost'
    };

    const dbToDisplayMap = {
        'new': 'New',
        'contacted': 'Contacted',
        'qualified': 'Qualified',
        'site visit': 'Site Visit',
        'proposal sent': 'Proposal Sent',
        'initial payment': 'Initial Payment',
        'completed': 'Completed',
        'lost': 'Lost'
    };

    const keyToDbMap = {};
    Object.keys(dbToKeyMap).forEach(dbValue => {
        keyToDbMap[dbToKeyMap[dbValue]] = dbValue;
    });

    let isMoving = false;

    function showToast(message, type = 'success') {
        const existingToast = document.getElementById('toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = `fixed top-4 right-4 sm:top-8 sm:right-8 z-[9999] px-4 py-3 sm:px-6 sm:py-4 rounded-xl bg-white shadow-xl border-l-4 ${type === 'success' ? 'border-green-500' : 'border-red-500'} transform translate-x-0 transition-transform duration-300 max-w-[calc(100%-2rem)] sm:max-w-sm`;
        toast.innerHTML = `<div class="text-slate-800 text-sm">${message}</div>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-[120%]');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Updated updateStats – only updates column count badges, no global stats
    function updateStats(fromStageKey, toStageKey) {
        const fromCountEl = document.getElementById(`count-${fromStageKey}`);
        const toCountEl = document.getElementById(`count-${toStageKey}`);

        if (fromCountEl) {
            let current = parseInt(fromCountEl.textContent);
            fromCountEl.textContent = current - 1;
            fromCountEl.classList.add('updating');
            setTimeout(() => fromCountEl.classList.remove('updating'), 500);
            if (current - 1 === 0) {
                fromCountEl.classList.remove('bg-[#0F286F]', 'text-white');
                fromCountEl.classList.add('bg-gray-200', 'text-gray-600');
            }
        }

        if (toCountEl) {
            let current = parseInt(toCountEl.textContent);
            toCountEl.textContent = current + 1;
            toCountEl.classList.add('updating');
            setTimeout(() => toCountEl.classList.remove('updating'), 500);
            if (current + 1 > 0) {
                toCountEl.classList.remove('bg-gray-200', 'text-gray-600');
                toCountEl.classList.add('bg-[#0F286F]', 'text-white');
            }
        }
    }

    // No longer needed – removed updateStatValues function

    // Update buttons on a card based on its new stage
    function updateCardButtons(cardElement, stageKey) {
        const actions = cardElement.querySelector('.lead-actions');
        if (!actions) return;

        actions.innerHTML = '';

        const isFirst = stageKey === 'new';
        const isLost = stageKey === 'lost';

        // Back button: show if not first
        if (!isFirst) {
            const backBtn = document.createElement('button');
            backBtn.type = 'button';
            backBtn.className = 'move-left bg-transparent border border-gray-200 rounded-lg px-2 sm:px-3 py-0.5 sm:py-1 text-[0.65rem] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-gray-100 hover:border-gray-400 active:scale-95';
            backBtn.textContent = '← Back';
            backBtn.onclick = function(e) {
                e.stopPropagation();
                moveLead(parseInt(cardElement.dataset.leadId), 'left', this);
            };
            actions.appendChild(backBtn);
        }

        // Next button: show if NOT lost
        if (!isLost) {
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'move-right bg-transparent border border-gray-200 rounded-lg px-2 sm:px-3 py-0.5 sm:py-1 text-[0.65rem] sm:text-xs cursor-pointer transition-colors text-gray-600 hover:bg-[#0F286F] hover:text-white hover:border-[#0F286F] active:scale-95';
            nextBtn.textContent = 'Next →';
            nextBtn.onclick = function(e) {
                e.stopPropagation();
                moveLead(parseInt(cardElement.dataset.leadId), 'right', this);
            };
            actions.appendChild(nextBtn);
        }
    }

    function smoothMoveLead(leadId, fromStageKey, toStageKey, direction) {
        const leadCard = document.getElementById(`lead-${leadId}`);
        if (!leadCard) return;

        const toBody = document.getElementById(`body-${toStageKey}`);
        const fromBody = document.getElementById(`body-${fromStageKey}`);
        if (!toBody || !fromBody) return;

        const slideClass = direction === 'left' ? 'slide-left' : 'slide-right';
        leadCard.classList.add(slideClass);

        setTimeout(() => {
            if (fromBody && leadCard.parentNode === fromBody) {
                fromBody.removeChild(leadCard);
                const remaining = fromBody.querySelectorAll('.lead-card');
                if (remaining.length === 0) {
                    let emptyEl = document.getElementById(`empty-${fromStageKey}`);
                    if (!emptyEl) {
                        emptyEl = document.createElement('div');
                        emptyEl.id = `empty-${fromStageKey}`;
                        emptyEl.className = 'empty-column text-center py-6 sm:py-8 text-gray-400 text-xs sm:text-sm';
                        emptyEl.innerHTML = '<div class="text-sm">No leads in this stage</div>';
                        fromBody.appendChild(emptyEl);
                    }
                }
            }

            const destEmpty = document.getElementById(`empty-${toStageKey}`);
            if (destEmpty) destEmpty.remove();

            leadCard.classList.remove(slideClass);
            leadCard.style.opacity = '0';
            leadCard.style.transform = 'scale(0.95)';

            const dbValue = keyToDbMap[toStageKey];
            leadCard.dataset.stage = dbValue || toStageKey;

            updateCardButtons(leadCard, toStageKey);

            toBody.appendChild(leadCard);

            requestAnimationFrame(() => {
                leadCard.classList.add('entering');
                leadCard.style.opacity = '1';
                leadCard.style.transform = 'scale(1)';
            });

            setTimeout(() => leadCard.classList.remove('entering'), 300);
            // Update column count badges only
            updateStats(fromStageKey, toStageKey);
        }, 300);
    }

    // ============================================================
    //  MOVE LEAD – fixed redirect handling + updates leadsData array
    // ============================================================
    function moveLead(leadId, direction, buttonElement) {
        if (isMoving) {
            showToast('Please wait, moving...', 'error');
            return;
        }

        const lead = leadsData.find(l => l.id === leadId);
        if (!lead) {
            showToast('Lead not found', 'error');
            return;
        }

        const currentKey = dbToKeyMap[lead.current_stage];
        if (!currentKey) {
            showToast('Invalid stage for this lead', 'error');
            return;
        }

        const currentIndex = stageOrder.indexOf(currentKey);
        let newIndex = direction === 'left' ? currentIndex - 1 : currentIndex + 1;

        if (newIndex < 0 || newIndex >= stageOrder.length) {
            showToast('Cannot move lead further in this direction', 'error');
            return;
        }

        const newKey = stageOrder[newIndex];
        const newDbValue = keyToDbMap[newKey];

        const originalText = buttonElement ? buttonElement.textContent : '';

        if (buttonElement) {
            buttonElement.disabled = true;
            buttonElement.innerHTML = '<span class="spinner"></span>';
        }

        isMoving = true;

        // Update UI optimistically
        smoothMoveLead(leadId, currentKey, newKey, direction);

        // Send request – follow redirects (your controller returns redirect)
        fetch(`/updateLeadStatus/${leadId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ current_stage: newDbValue }),
            redirect: 'follow'   // <-- changed from 'manual' to 'follow'
        })
        .then(response => {
            // After following redirects, the final response should be 200 OK
            if (response.ok) {
                showToast(`✓ Moved to ${stageLabels[newKey]}`, 'success');
                // 🔁 Update the local leadsData so next move works from the correct stage
                lead.current_stage = newDbValue;
            } else {
                throw new Error(`Unexpected response: ${response.status}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to move lead: ' + error.message, 'error');
            // Reload to reset any inconsistent UI state
            window.location.reload();
        })
        .finally(() => {
            isMoving = false;
            if (buttonElement && buttonElement.parentNode) {
                buttonElement.disabled = false;
                buttonElement.textContent = originalText;
            }
        });
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

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => closeModal(modal.id));
        }
    });

    function showLeadDetails(leadId) {
        const lead = leadsData.find(l => l.id === leadId);
        if (!lead) {
            showToast('Lead not found', 'error');
            return;
        }

        document.getElementById('leadDetailName').textContent = lead.full_name;
        const displayStage = dbToDisplayMap[lead.current_stage] || lead.current_stage || 'Unknown';
        document.getElementById('leadDetailStage').textContent = `Stage: ${displayStage}`;

        const content = document.getElementById('leadDetailContent');
        content.innerHTML = `
            <div class="col-span-1 sm:col-span-2 lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Full Name</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.full_name || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Email</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.email || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Phone</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.phone || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Lead Type</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${capitalize(lead.lead_type) || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Lead Source</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${capitalize(lead.lead_source) || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Budget Range</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.budget_range || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Preferred Location</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.preferred_location || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Current Stage</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${dbToDisplayMap[lead.current_stage] || lead.current_stage || 'Unknown'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Agent ID</div>
                <div class="text-sm sm:text-base text-gray-800 py-1">${lead.agent_id || 'Not assigned'}</div>
            </div>
        `;
        openModal('leadDetailModal');
    }

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    let draggedLeadId = null;

    function handleDragStart(event, leadId) {
        draggedLeadId = leadId;
        event.dataTransfer.effectAllowed = 'move';
        const card = document.getElementById(`lead-${leadId}`);
        if (card) card.classList.add('dragging');
    }

    function handleDrop(event, targetStageKey) {
        event.preventDefault();
        const column = event.target.closest('.kanban-column');
        if (column) column.classList.remove('drag-over');

        if (!draggedLeadId || isMoving) return;

        const lead = leadsData.find(l => l.id === draggedLeadId);
        if (!lead) {
            draggedLeadId = null;
            showToast('Lead not found', 'error');
            return;
        }

        const currentKey = dbToKeyMap[lead.current_stage];
        if (!currentKey || currentKey === targetStageKey) {
            draggedLeadId = null;
            return;
        }

        const currentIndex = stageOrder.indexOf(currentKey);
        const targetIndex = stageOrder.indexOf(targetStageKey);
        const direction = targetIndex > currentIndex ? 'right' : 'left';

        const card = document.getElementById(`lead-${draggedLeadId}`);
        if (card) {
            const button = card.querySelector(direction === 'right' ? '.move-right' : '.move-left');
            moveLead(draggedLeadId, direction, button);
        }
        draggedLeadId = null;
    }

    document.querySelectorAll('.kanban-column-body').forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            const kanbanColumn = this.closest('.kanban-column');
            if (kanbanColumn) kanbanColumn.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function(e) {
            const kanbanColumn = this.closest('.kanban-column');
            if (kanbanColumn) kanbanColumn.classList.remove('drag-over');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-[120%]');
                setTimeout(() => toast.style.display = 'none', 300);
            }, 3000);
        }

        document.addEventListener('dragend', function() {
            document.querySelectorAll('.lead-card.dragging').forEach(c => c.classList.remove('dragging'));
            document.querySelectorAll('.kanban-column.drag-over').forEach(c => c.classList.remove('drag-over'));
            draggedLeadId = null;
        });
    });
</script>

@endsection