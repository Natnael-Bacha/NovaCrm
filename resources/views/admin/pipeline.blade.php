@extends('layouts.app')

@section('title', 'Pipeline · NovaTra')

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@push('styles')
<style>
    /* Pipeline Styles */
    .pipeline-container {
        padding: 1.5rem;
        min-height: 100vh;
        overflow-y: auto;
    }

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
    
    .btn-primary {
        background-color: #0F286F;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-secondary:hover {
        background: #f8faff;
    }
    
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #f1f4f9;
        transition: all 0.3s ease;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0F286F;
        transition: all 0.3s ease;
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

    /* Kanban Board */
    .kanban-board-wrapper {
        overflow-x: auto;
        padding-bottom: 1rem;
        margin: 0 -0.5rem;
    }

    .kanban-board {
        display: flex;
        gap: 1.25rem;
        padding: 0.5rem;
        min-height: 500px;
        align-items: flex-start;
        min-width: min-content;
    }

    .kanban-column {
        min-width: 280px;
        max-width: 320px;
        flex: 0 0 auto;
        background: white;
        border-radius: 1rem;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        max-height: 65vh;
        transition: all 0.3s ease;
    }

    .kanban-column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #0F286F;
        flex-shrink: 0;
    }

    .kanban-column-title {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #0F286F;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .kanban-column-count {
        background: #e2e8f0;
        padding: 0.125rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
        transition: all 0.3s ease;
    }

    .kanban-column-count.has-leads {
        background: #0F286F;
        color: white;
    }

    .kanban-column-count.updating {
        animation: pulse-count 0.5s ease;
    }

    @keyframes pulse-count {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }

    .kanban-column-body {
        flex: 1;
        overflow-y: auto;
        padding-right: 0.25rem;
        min-height: 100px;
        position: relative;
    }

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

    /* Lead Card - Smooth transitions */
    .lead-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #0F286F;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform, opacity;
    }

    .lead-card:last-child {
        margin-bottom: 0;
    }

    .lead-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(15, 40, 111, 0.1);
        border-color: #0F286F;
    }

    /* Slide animations for moving cards */
    .lead-card.slide-left {
        animation: slideLeft 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .lead-card.slide-right {
        animation: slideRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideLeft {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateX(-30px) scale(0.95);
        }
    }

    @keyframes slideRight {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateX(30px) scale(0.95);
        }
    }

    /* Entrance animation */
    .lead-card.entering {
        animation: enter 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes enter {
        0% {
            opacity: 0;
            transform: scale(0.95);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .lead-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .lead-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        flex: 1;
        margin-right: 0.5rem;
    }

    .lead-type-badge {
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        letter-spacing: 0.025em;
        flex-shrink: 0;
        background: #0F286F;
        color: white;
    }

    .lead-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .lead-details-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        white-space: nowrap;
        overflow: hidden;
    }

    .lead-details-item .label {
        font-weight: 500;
        color: #4b5563;
        min-width: 50px;
        flex-shrink: 0;
    }

    .lead-details-item .value {
        color: #6b7280;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lead-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f1f4f9;
    }

    .lead-actions button {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #4b5563;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .lead-actions button:active {
        transform: scale(0.95);
    }

    .lead-actions .move-left:hover {
        background: #f1f4f9;
        border-color: #9ca3af;
    }

    .lead-actions .move-right:hover {
        background: #0F286F;
        color: white;
        border-color: #0F286F;
    }

    .lead-actions button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Empty state */
    .empty-column {
        text-align: center;
        padding: 2rem 0;
        color: #9ca3af;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .empty-column-text {
        font-size: 0.8rem;
    }

    /* Modal */
    .lead-detail-modal .modal-content {
        max-width: 600px;
    }

    .lead-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .lead-detail-field {
        margin-bottom: 0.5rem;
    }

    .lead-detail-field-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.125rem;
    }

    .lead-detail-field-value {
        font-size: 0.95rem;
        color: #1e293b;
        word-break: break-word;
        padding: 0.25rem 0;
    }

    .lead-detail-full {
        grid-column: 1 / -1;
    }

    /* Drag and drop styles */
    .lead-card.dragging {
        opacity: 0.4;
        transform: scale(0.95);
    }

    .kanban-column.drag-over {
        background: #f8faff;
        border-color: #0F286F;
        border-style: dashed;
    }

    /* Loading spinner for button */
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

    /* Responsive */
    @media (max-width: 1024px) {
        .kanban-column {
            min-width: 250px;
            max-width: 280px;
            max-height: 60vh;
        }
    }

    @media (max-width: 768px) {
        .pipeline-container {
            padding: 1rem;
        }

        .kanban-column {
            min-width: 220px;
            max-width: 250px;
            padding: 0.75rem;
            max-height: 50vh;
        }

        .lead-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .kanban-column {
            min-width: 200px;
            max-width: 220px;
            max-height: 45vh;
        }

        .pipeline-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')

<div class="pipeline-container">
    
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
            <h1 class="text-3xl font-bold" style="color: #0F286F;">Pipeline</h1>
            <p class="text-gray-500 mt-1 text-sm">Track your leads through each stage of the sales process</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8" id="statsContainer">
        <div class="stat-card" id="statTotal">
            <div class="stat-value" id="statTotalValue">{{ $leads->count() }}</div>
            <div class="stat-label">Total Leads</div>
        </div>
        <div class="stat-card" id="statNew">
            <div class="stat-value" id="statNewValue">{{ $leads->where('current_stage', 'new')->count() }}</div>
            <div class="stat-label">New</div>
        </div>
        <div class="stat-card" id="statInProgress">
            <div class="stat-value" id="statInProgressValue">
                {{ $leads->where('current_stage', 'contacted')->count() + 
                   $leads->where('current_stage', 'qualified')->count() + 
                   $leads->where('current_stage', 'site visit')->count() +
                   $leads->where('current_stage', 'proposal sent')->count() +
                   $leads->where('current_stage', 'initial payment')->count() }}
            </div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card" id="statCompleted">
            <div class="stat-value" id="statCompletedValue">{{ $leads->where('current_stage', 'completed')->count() }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board-wrapper">
        <div class="kanban-board" id="kanbanBoard">
            @php
                // Define stages with their exact database values and display labels
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
                    // Get leads that match the exact database value for this stage
                    $stageLeads = $leads->where('current_stage', $stage['db_value']);
                    $hasLeads = $stageLeads->count() > 0;
                @endphp
                <div class="kanban-column" data-stage="{{ $stageKey }}" id="column-{{ $stageKey }}">
                    <div class="kanban-column-header">
                        <div class="kanban-column-title">
                            {{ $stage['label'] }}
                        </div>
                        <span class="kanban-column-count {{ $hasLeads ? 'has-leads' : '' }}" id="count-{{ $stageKey }}">
                            {{ $stageLeads->count() }}
                        </span>
                    </div>
                    <div class="kanban-column-body" 
                         id="body-{{ $stageKey }}"
                         ondragover="event.preventDefault();"
                         ondrop="handleDrop(event, '{{ $stageKey }}')">
                        @forelse($stageLeads as $lead)
                        <div class="lead-card"
                             id="lead-{{ $lead->id }}"
                             draggable="true"
                             data-lead-id="{{ $lead->id }}"
                             data-stage="{{ $lead->current_stage }}"
                             ondragstart="handleDragStart(event, {{ $lead->id }})"
                             onclick="showLeadDetails({{ $lead->id }})">
                            <div class="lead-card-header">
                                <span class="lead-name">{{ $lead->full_name }}</span>
                                <span class="lead-type-badge">
                                    {{ ucfirst($lead->lead_type ?? 'Other') }}
                                </span>
                            </div>
                            <div class="lead-details">
                                @if($lead->email)
                                <div class="lead-details-item">
                                    <span class="label">Email:</span>
                                    <span class="value">{{ $lead->email }}</span>
                                </div>
                                @endif
                                @if($lead->phone)
                                <div class="lead-details-item">
                                    <span class="label">Phone:</span>
                                    <span class="value">{{ $lead->phone }}</span>
                                </div>
                                @endif
                                @if($lead->budget_range)
                                <div class="lead-details-item">
                                    <span class="label">Budget:</span>
                                    <span class="value">{{ $lead->budget_range }}</span>
                                </div>
                                @endif
                                @if($lead->preferred_location)
                                <div class="lead-details-item">
                                    <span class="label">Location:</span>
                                    <span class="value">{{ $lead->preferred_location }}</span>
                                </div>
                                @endif
                                @if($lead->lead_source)
                                <div class="lead-details-item">
                                    <span class="label">Source:</span>
                                    <span class="value">{{ ucfirst($lead->lead_source) }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="lead-actions" onclick="event.stopPropagation();">
                                <!-- Back button - shown if not in first stage -->
                                @if($stageKey != 'new')
                                <button class="move-left" onclick="moveLead({{ $lead->id }}, 'left', this)">
                                    ← Back
                                </button>
                                @endif
                                <!-- Next button - shown if not in last two stages (completed or lost) -->
                                @if($stageKey != 'completed' && $stageKey != 'lost')
                                <button class="move-right" onclick="moveLead({{ $lead->id }}, 'right', this)">
                                    Next →
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-column" id="empty-{{ $stageKey }}">
                            <div class="empty-column-text">No leads in this stage</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Lead Detail Modal - View Only -->
    <div id="leadDetailModal" class="modal-overlay lead-detail-modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold" style="color: #0F286F;" id="leadDetailName">Lead Details</h2>
                    <p class="text-sm text-gray-500 mt-0.5" id="leadDetailStage">Stage: New</p>
                </div>
                <button onclick="closeModal('leadDetailModal')" class="text-gray-400 hover:text-gray-600 transition-colors text-2xl">×</button>
            </div>

            <div id="leadDetailContent" class="lead-detail-grid">
                <!-- Will be populated by JavaScript -->
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
                <button onclick="closeModal('leadDetailModal')" class="btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    // Store leads data
    const leadsData = @json($leads);
    
    // Stage order for navigation (using the same keys as defined in PHP)
    const stageOrder = ['new', 'contacted', 'qualified', 'site_visit', 'proposal_sent', 'initial_payment', 'completed', 'lost'];
    
    // Map stage keys to display labels
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

    // Map database values to stage keys
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

    // Map database values to display labels
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

    // Reverse map: stage key to database value
    const keyToDbMap = {};
    Object.keys(dbToKeyMap).forEach(dbValue => {
        keyToDbMap[dbToKeyMap[dbValue]] = dbValue;
    });

    let isMoving = false;

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
        }, 3000);
    }

    // Update stats with animation
    function updateStats(fromStageKey, toStageKey) {
        // Update the column counts
        const fromCountEl = document.getElementById(`count-${fromStageKey}`);
        const toCountEl = document.getElementById(`count-${toStageKey}`);
        
        if (fromCountEl) {
            const currentCount = parseInt(fromCountEl.textContent);
            fromCountEl.textContent = currentCount - 1;
            fromCountEl.classList.add('updating');
            setTimeout(() => fromCountEl.classList.remove('updating'), 500);
            
            // Update has-leads class
            if (currentCount - 1 === 0) {
                fromCountEl.classList.remove('has-leads');
            }
        }
        
        if (toCountEl) {
            const currentCount = parseInt(toCountEl.textContent);
            toCountEl.textContent = currentCount + 1;
            toCountEl.classList.add('updating');
            setTimeout(() => toCountEl.classList.remove('updating'), 500);
            
            // Update has-leads class
            if (currentCount + 1 > 0) {
                toCountEl.classList.add('has-leads');
            }
        }

        // Update stats
        updateStatValues();
    }

    // Update stat values
    function updateStatValues() {
        const total = document.querySelectorAll('.lead-card').length;
        document.getElementById('statTotalValue').textContent = total;
        
        // Count leads in each stage for stats
        const newCount = document.querySelectorAll('#column-new .lead-card').length;
        const contactedCount = document.querySelectorAll('#column-contacted .lead-card').length;
        const qualifiedCount = document.querySelectorAll('#column-qualified .lead-card').length;
        const siteVisitCount = document.querySelectorAll('#column-site_visit .lead-card').length;
        const proposalSentCount = document.querySelectorAll('#column-proposal_sent .lead-card').length;
        const initialPaymentCount = document.querySelectorAll('#column-initial_payment .lead-card').length;
        const completedCount = document.querySelectorAll('#column-completed .lead-card').length;
        
        document.getElementById('statNewValue').textContent = newCount;
        document.getElementById('statInProgressValue').textContent = contactedCount + qualifiedCount + siteVisitCount + proposalSentCount + initialPaymentCount;
        document.getElementById('statCompletedValue').textContent = completedCount;
    }

    // Update buttons based on stage
    function updateCardButtons(cardElement, stageKey) {
        const actions = cardElement.querySelector('.lead-actions');
        if (!actions) return;

        // Clear existing buttons
        actions.innerHTML = '';

        const stageIndex = stageOrder.indexOf(stageKey);
        const isFirst = stageIndex === 0;
        const isCompletedOrLost = stageKey === 'completed' || stageKey === 'lost';

        // Create Back button (show if not first)
        if (!isFirst) {
            const backBtn = document.createElement('button');
            backBtn.className = 'move-left';
            backBtn.textContent = '← Back';
            backBtn.onclick = function(e) {
                e.stopPropagation();
                moveLead(parseInt(cardElement.dataset.leadId), 'left', this);
            };
            actions.appendChild(backBtn);
        }

        // Create Next button (show if not completed or lost)
        if (!isCompletedOrLost) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'move-right';
            nextBtn.textContent = 'Next →';
            nextBtn.onclick = function(e) {
                e.stopPropagation();
                moveLead(parseInt(cardElement.dataset.leadId), 'right', this);
            };
            actions.appendChild(nextBtn);
        }
    }

    // Smooth move without DOM removal
    function smoothMoveLead(leadId, fromStageKey, toStageKey, direction) {
        const leadCard = document.getElementById(`lead-${leadId}`);
        if (!leadCard) return;

        // Get the destination column body
        const toBody = document.getElementById(`body-${toStageKey}`);
        const fromBody = document.getElementById(`body-${fromStageKey}`);
        
        if (!toBody || !fromBody) return;

        // Determine slide direction
        const slideClass = direction === 'left' ? 'slide-left' : 'slide-right';
        
        // Add slide out animation
        leadCard.classList.add(slideClass);
        
        // After slide animation completes
        setTimeout(() => {
            // Remove from source
            if (fromBody && leadCard.parentNode === fromBody) {
                fromBody.removeChild(leadCard);
                
                // Check if source column is empty and show empty state
                const remainingCards = fromBody.querySelectorAll('.lead-card');
                if (remainingCards.length === 0) {
                    let emptyEl = document.getElementById(`empty-${fromStageKey}`);
                    if (!emptyEl) {
                        emptyEl = document.createElement('div');
                        emptyEl.id = `empty-${fromStageKey}`;
                        emptyEl.className = 'empty-column';
                        emptyEl.innerHTML = '<div class="empty-column-text">No leads in this stage</div>';
                        fromBody.appendChild(emptyEl);
                    }
                }
            }

            // Remove empty state from destination if it exists
            const destEmptyEl = document.getElementById(`empty-${toStageKey}`);
            if (destEmptyEl) {
                destEmptyEl.remove();
            }

            // Reset card styles and add to destination
            leadCard.classList.remove(slideClass);
            leadCard.style.opacity = '0';
            leadCard.style.transform = 'scale(0.95)';
            
            // Update card data
            const dbValue = keyToDbMap[toStageKey];
            leadCard.dataset.stage = dbValue || toStageKey;
            
            // Update buttons based on new stage
            updateCardButtons(leadCard, toStageKey);
            
            // Append to destination
            toBody.appendChild(leadCard);
            
            // Trigger entrance animation
            requestAnimationFrame(() => {
                leadCard.classList.add('entering');
                leadCard.style.opacity = '1';
                leadCard.style.transform = 'scale(1)';
            });
            
            // Remove entrance class after animation
            setTimeout(() => {
                leadCard.classList.remove('entering');
            }, 300);

            // Update counts
            updateStats(fromStageKey, toStageKey);
            
        }, 300); // Match animation duration
    }

    // Move lead between stages with smooth animation
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

        // Get the current stage key
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

        // Disable button and show loading state
        if (buttonElement) {
            buttonElement.disabled = true;
            const originalText = buttonElement.textContent;
            buttonElement.innerHTML = '<span class="spinner"></span>';
        }

        // Smooth optimistic UI update
        isMoving = true;
        smoothMoveLead(leadId, currentKey, newKey, direction);

        // Send update to server
        fetch(`/leads/${leadId}/stage`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ stage: newDbValue })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(`✓ Moved to ${stageLabels[newKey]}`, 'success');
                // Update the lead data
                lead.current_stage = newDbValue;
            } else {
                // Rollback on error
                showToast(data.message || 'Failed to move lead', 'error');
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to move lead: ' + error.message, 'error');
            setTimeout(() => window.location.reload(), 1000);
        })
        .finally(() => {
            isMoving = false;
            if (buttonElement) {
                buttonElement.disabled = false;
                const originalText = buttonElement.textContent.includes('←') ? '← Back' : 'Next →';
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

    // Show lead details
    function showLeadDetails(leadId) {
        const lead = leadsData.find(l => l.id === leadId);
        if (!lead) {
            console.error('Lead not found with ID:', leadId);
            showToast('Lead not found', 'error');
            return;
        }
        
        document.getElementById('leadDetailName').textContent = lead.full_name;
        const displayStage = dbToDisplayMap[lead.current_stage] || lead.current_stage || 'Unknown';
        document.getElementById('leadDetailStage').textContent = `Stage: ${displayStage}`;

        const content = document.getElementById('leadDetailContent');
        content.innerHTML = `
            <div class="lead-detail-field lead-detail-full">
                <div class="lead-detail-field-label">Full Name</div>
                <div class="lead-detail-field-value">${lead.full_name || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Email</div>
                <div class="lead-detail-field-value">${lead.email || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Phone</div>
                <div class="lead-detail-field-value">${lead.phone || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Lead Type</div>
                <div class="lead-detail-field-value">${capitalize(lead.lead_type) || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Lead Source</div>
                <div class="lead-detail-field-value">${capitalize(lead.lead_source) || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Budget Range</div>
                <div class="lead-detail-field-value">${lead.budget_range || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Preferred Location</div>
                <div class="lead-detail-field-value">${lead.preferred_location || 'N/A'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Current Stage</div>
                <div class="lead-detail-field-value">${dbToDisplayMap[lead.current_stage] || lead.current_stage || 'Unknown'}</div>
            </div>
            <div class="lead-detail-field">
                <div class="lead-detail-field-label">Agent ID</div>
                <div class="lead-detail-field-value">${lead.agent_id || 'Not assigned'}</div>
            </div>
        `;

        openModal('leadDetailModal');
    }

    // Helper to capitalize strings
    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Drag and drop functionality
    let draggedLeadId = null;

    function handleDragStart(event, leadId) {
        draggedLeadId = leadId;
        event.dataTransfer.effectAllowed = 'move';
        const card = document.getElementById(`lead-${leadId}`);
        if (card) {
            card.classList.add('dragging');
        }
    }

    function handleDrop(event, targetStageKey) {
        event.preventDefault();
        const column = event.target.closest('.kanban-column');
        if (column) {
            column.classList.remove('drag-over');
        }

        if (!draggedLeadId || isMoving) {
            return;
        }

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

        // Determine direction for the move
        const currentIndex = stageOrder.indexOf(currentKey);
        const targetIndex = stageOrder.indexOf(targetStageKey);
        const direction = targetIndex > currentIndex ? 'right' : 'left';

        // Use the move function with smooth animation
        const card = document.getElementById(`lead-${draggedLeadId}`);
        if (card) {
            const button = card.querySelector(direction === 'right' ? '.move-right' : '.move-left');
            moveLead(draggedLeadId, direction, button);
        }

        draggedLeadId = null;
    }

    // Add drag-over visual feedback
    document.querySelectorAll('.kanban-column-body').forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            const kanbanColumn = this.closest('.kanban-column');
            if (kanbanColumn) {
                kanbanColumn.classList.add('drag-over');
            }
        });

        column.addEventListener('dragleave', function(e) {
            const kanbanColumn = this.closest('.kanban-column');
            if (kanbanColumn) {
                kanbanColumn.classList.remove('drag-over');
            }
        });
    });

    // Auto-hide toast after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 3000);
        }

        // Fix for drag end
        document.addEventListener('dragend', function() {
            document.querySelectorAll('.lead-card.dragging').forEach(card => {
                card.classList.remove('dragging');
            });
            document.querySelectorAll('.kanban-column.drag-over').forEach(col => {
                col.classList.remove('drag-over');
            });
            draggedLeadId = null;
        });
    });
</script>

@endsection