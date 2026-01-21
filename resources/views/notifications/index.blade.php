@extends('layouts.internal')

@section('title', 'Notifications')

@section('content')

<style>
    .alert-item {
        border-left: 5px solid transparent;
        transition: transform 0.2s;
        border-radius: 10px;
    }
    .alert-item:hover {
        transform: translateX(5px);
        background-color: #fcfcfc;
    }
    
    .alert-overdue { border-left-color: var(--soft-red); background-color: #fff5f5; }
    .alert-upcoming { border-left-color: var(--green); background-color: #f0fff4; }
    .alert-stock { border-left-color: var(--orange); background-color: #fffaf0; }

    .nav-pills .nav-link.active {
        background-color: var(--green);
        border-radius: 50px;
    }
    .nav-pills .nav-link {
        color: #555;
        font-weight: bold;
        padding: 10px 25px;
    }

    /* Floating Action Button Style */
    .fab-contact {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--green);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(46, 139, 87, 0.4);
        border: none;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    .fab-contact:hover {
        transform: scale(1.1);
        background-color: #246d43;
        color: white;
    }
</style>

<div id="main-content">
    <div class="container-fluid">
        
        <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="header-icon-bg me-3">
                    <i class="fa-solid fa-bell me-2"></i>
                </div>
                <div>
                    <h1 class="mb-1 header-title">Health Alerts & Notifications</h1>
                    <p class="text-muted mb-0 header-subtitle">
                        Monitor patient schedules and critical inventory levels in real-time.
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <form action="{{ route('notifications.markAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check-double me-2"></i>Mark All as Read
                    </button>
                </form>
            </div>
        </div>

        <ul class="nav nav-pills mb-4 bg-white p-2 rounded-pill shadow-sm d-inline-flex" id="notifTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#all">All Alerts ({{ $counts['all'] }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#overdue">Overdue ({{ $counts['overdue'] }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#upcoming">Upcoming ({{ $counts['upcoming'] }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#inventory">Inventory ({{ $counts['inventory'] }})</a></li>
        </ul>

        <div class="tab-content">
            {{-- ALL ALERTS TAB --}}
            <div class="tab-pane fade show active" id="all">
                @forelse($notifications as $notif)
                    @php
                        // Map notification types to CSS classes and icons
                        $config = match($notif->type) {
                            'overdue' => ['class' => 'alert-overdue', 'icon' => 'fa-clock-rotate-left', 'bg' => 'bg-danger', 'text' => 'text-danger'],
                            'upcoming' => ['class' => 'alert-upcoming', 'icon' => 'fa-calendar-check', 'bg' => 'bg-success', 'text' => 'text-success'],
                            'inventory' => ['class' => 'alert-stock', 'icon' => 'fa-triangle-exclamation', 'bg' => 'bg-warning', 'text' => 'text-warning'],
                            default => ['class' => '', 'icon' => 'fa-bell', 'bg' => 'bg-primary', 'text' => 'text-primary'],
                        };
                    @endphp

                    <div class="alert-item {{ $config['class'] }} shadow-sm p-3 mb-3 border {{ $notif->is_read ? 'opacity-75' : 'fw-bold' }}">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="{{ $config['bg'] }} {{ $notif->type == 'inventory' ? 'text-dark' : 'text-white' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fa-solid {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold {{ $config['text'] }}">{{ $notif->message }}</h6>
                                
                                {{-- VACCINE-RELATED NOTIFICATIONS (Overdue & Upcoming) --}}
                                @if(isset($notif->child_id))
                                    @php
                                        $child = \App\Models\Child::with('guardian')->find($notif->child_id);
                                    @endphp
                                    @if($child)
                                        <p class="mb-0 small text-dark">
                                            Patient: <strong>{{ $notif->child_name }}</strong> 
                                            @if($notif->type == 'overdue')
                                                • <span class="text-danger">Action Required</span>
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-phone me-1"></i> 
                                            Guardian: {{ $child->guardian->grd_first_name ?? 'N/A' }} ({{ $child->guardian->grd_contact_number ?? 'No Number' }})
                                        </small>
                                    @endif
                                @endif

                                {{-- INVENTORY NOTIFICATIONS --}}
                                @if(isset($notif->inventory_id))
                                    <p class="mb-0 small text-dark">
                                        Batch: <strong>{{ $notif->batch_number ?? 'N/A' }}</strong> • 
                                        Qty: <strong>{{ $notif->quantity ?? 0 }}</strong>
                                        @if(isset($notif->expiry_date))
                                            • Expires: <strong>{{ \Carbon\Carbon::parse($notif->expiry_date)->format('M d, Y') }}</strong>
                                        @endif
                                    </p>
                                @endif

                                <div class="mt-1">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        @if(isset($notif->days_overdue))
                                            {{ $notif->days_overdue }} day(s) overdue
                                        @elseif(isset($notif->days_until_due))
                                            Due in {{ $notif->days_until_due }} day(s)
                                        @elseif(isset($notif->days_until_expiry))
                                            Expires in {{ $notif->days_until_expiry }} day(s)
                                        @elseif(isset($notif->days_expired))
                                            Expired {{ $notif->days_expired }} day(s) ago
                                        @else
                                            {{ $notif->created_at->diffForHumans() }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                {{-- VACCINE NOTIFICATION ACTIONS --}}
                                @if(isset($notif->child_id))
                                    @php
                                        $child = \App\Models\Child::with('guardian')->find($notif->child_id);
                                    @endphp
                                    @if($child)
                                        <button class="btn btn-sm {{ str_replace('text', 'btn', $config['text']) }} rounded-pill px-3 fw-bold" 
                                                onclick="prepareModal('{{ $notif->child_name }}', '{{ $child->guardian->grd_contact_number ?? 'N/A' }}', '{{ $notif->type }}', '{{ $notif->vaccine_name ?? 'Vaccination' }}', {{ $notif->dose_number ?? 1 }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#contactModal">
                                            {{ $notif->type == 'overdue' ? 'Call Parent' : 'Remind' }}
                                        </button>
                                    @endif
                                @endif

                                {{-- INVENTORY NOTIFICATION ACTIONS --}}
                                @if(isset($notif->inventory_id))
                                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">Restock</a>
                                @endif
                                
                                {{-- MARK AS READ BUTTON --}}
                                <form action="{{ route('notifications.markRead', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>

                                {{-- DISMISS BUTTON --}}
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Dismiss">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No notifications found.</p>
                    </div>
                @endforelse
            </div>

            {{-- OVERDUE TAB --}}
            <div class="tab-pane fade" id="overdue">
                @forelse($notifications->where('type', 'overdue') as $notif)
                    @php
                        $config = ['class' => 'alert-overdue', 'icon' => 'fa-clock-rotate-left', 'bg' => 'bg-danger', 'text' => 'text-danger'];
                        $child = \App\Models\Child::with('guardian')->find($notif->child_id);
                    @endphp

                    <div class="alert-item {{ $config['class'] }} shadow-sm p-3 mb-3 border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="{{ $config['bg'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fa-solid {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold {{ $config['text'] }}">{{ $notif->message }}</h6>
                                @if($child)
                                    <p class="mb-0 small text-dark">
                                        Patient: <strong>{{ $notif->child_name }}</strong> • <span class="text-danger">Action Required</span>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-phone me-1"></i> 
                                        Guardian: {{ $child->guardian->grd_first_name ?? 'N/A' }} ({{ $child->guardian->grd_contact_number ?? 'No Number' }})
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-3 text-end">
                                @if($child)
                                    <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" 
                                            onclick="prepareModal('{{ $notif->child_name }}', '{{ $child->guardian->grd_contact_number ?? 'N/A' }}', 'overdue', '{{ $notif->vaccine_name ?? 'Vaccination' }}', {{ $notif->dose_number ?? 1 }})"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#contactModal">
                                        Call Parent
                                    </button>
                                @endif
                                <form action="{{ route('notifications.markRead', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Dismiss">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No overdue vaccines found.</p>
                    </div>
                @endforelse
            </div>

            {{-- UPCOMING TAB --}}
            <div class="tab-pane fade" id="upcoming">
                @forelse($notifications->where('type', 'upcoming') as $notif)
                    @php
                        $config = ['class' => 'alert-upcoming', 'icon' => 'fa-calendar-check', 'bg' => 'bg-success', 'text' => 'text-success'];
                        $child = \App\Models\Child::with('guardian')->find($notif->child_id);
                    @endphp

                    <div class="alert-item {{ $config['class'] }} shadow-sm p-3 mb-3 border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="{{ $config['bg'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fa-solid {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold {{ $config['text'] }}">{{ $notif->message }}</h6>
                                @if($child)
                                    <p class="mb-0 small text-dark">
                                        Patient: <strong>{{ $notif->child_name }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-phone me-1"></i> 
                                        Guardian: {{ $child->guardian->grd_first_name ?? 'N/A' }} ({{ $child->guardian->grd_contact_number ?? 'No Number' }})
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-3 text-end">
                                @if($child)
                                    <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold" 
                                            onclick="prepareModal('{{ $notif->child_name }}', '{{ $child->guardian->grd_contact_number ?? 'N/A' }}', 'upcoming', '{{ $notif->vaccine_name ?? 'Vaccination' }}', {{ $notif->dose_number ?? 1 }})"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#contactModal">
                                        Remind
                                    </button>
                                @endif
                                <form action="{{ route('notifications.markRead', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Dismiss">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa-solid fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No upcoming vaccines found.</p>
                    </div>
                @endforelse
            </div>

            {{-- INVENTORY TAB --}}
            <div class="tab-pane fade" id="inventory">
                @forelse($notifications->where('type', 'inventory') as $notif)
                    @php
                        $config = ['class' => 'alert-stock', 'icon' => 'fa-triangle-exclamation', 'bg' => 'bg-warning', 'text' => 'text-warning'];
                    @endphp

                    <div class="alert-item {{ $config['class'] }} shadow-sm p-3 mb-3 border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="{{ $config['bg'] }} text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fa-solid {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold {{ $config['text'] }}">{{ $notif->message }}</h6>
                                <p class="mb-0 small text-dark">
                                    Batch: <strong>{{ $notif->batch_number ?? 'N/A' }}</strong> • 
                                    Qty: <strong>{{ $notif->quantity ?? 0 }}</strong>
                                    @if(isset($notif->expiry_date))
                                        • Expires: <strong>{{ \Carbon\Carbon::parse($notif->expiry_date)->format('M d, Y') }}</strong>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">Restock</a>
                                <form action="{{ route('notifications.markRead', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" title="Dismiss">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa-solid fa-boxes-stacked fa-3x text-muted mb-3"></i>
                        <p class="text-muted">All inventory levels are good.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <button class="fab-contact" data-bs-toggle="modal" data-bs-target="#contactModal" title="Quick Contact">
        <i class="fa-solid fa-comment-dots"></i>
    </button>

    {{-- CONTACT MODAL --}}
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold" id="contactModalLabel">
                        <i class="fa-solid fa-paper-plane me-2"></i>Contact Guardian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background-color: var(--cream); border: 1px dashed var(--green);">
                        <div class="flex-grow-1">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 7pt;">Patient Name</small>
                            <h6 class="fw-bold mb-0 text-dark" id="modalPatientName">---</h6>
                        </div>
                        <div class="text-end">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 7pt;">Guardian Number</small>
                            <h6 class="fw-bold mb-0 text-success" id="modalPatientPhone">---</h6>
                        </div>
                    </div>

                    <form id="smsForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">CHOOSE MESSAGE TEMPLATE</label>
                            <select class="form-select border-2 rounded-pill" id="messageTemplate" onchange="updateMessage()">
                                <option value="overdue">Overdue Reminder (Urgent)</option>
                                <option value="upcoming">Upcoming Schedule (Friendly)</option>
                                <option value="followup">General Follow-up</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">MESSAGE CONTENT</label>
                            <textarea class="form-control border-2 shadow-sm" id="messageBody" rows="5" style="border-radius: 15px; font-size: 9pt;"></textarea>
                            <div class="text-end mt-1">
                                <small class="text-muted" id="charCount">Characters: 0</small>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-8">
                                <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">
                                    <i class="fa-solid fa-comment-sms me-2"></i>SEND SMS
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-dark w-100 py-2 rounded-pill fw-bold" onclick="logCall()">
                                    <i class="fa-solid fa-phone me-1"></i> LOG CALL
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light border-0 justify-content-center" style="border-radius: 0 0 20px 20px;">
                    <p class="mb-0 x-small text-muted">Message will be sent via the <strong>ImmuniSprout SMS Gateway</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables to keep track of current child being messaged
    let currentChild = "";
    let currentVaccine = "";
    let currentDose = 1;

    function prepareModal(childName, phone, type, vaccineName, doseNumber = 1) {
        currentChild = childName;
        currentVaccine = vaccineName;
        currentDose = doseNumber;
        
        document.getElementById('modalPatientName').innerText = childName;
        document.getElementById('modalPatientPhone').innerText = phone;
        
        // Auto-select template based on alert type
        document.getElementById('messageTemplate').value = type;
        
        // Trigger message update
        updateMessage();
    }

    function updateMessage() {
        const template = document.getElementById('messageTemplate').value;
        const body = document.getElementById('messageBody');
        const child = currentChild || "your child";
        const vaccine = currentVaccine || "scheduled vaccination";
        const dose = currentDose > 1 ? ` (Dose ${currentDose})` : "";
        
        const messages = {
            overdue: `Good day! This is Brgy. Pusok Health Center. ${child} is OVERDUE for the ${vaccine}${dose}. Please visit us as soon as possible. Stay safe!`,
            upcoming: `Reminder from Brgy. Pusok Health Center: ${child} is scheduled for ${vaccine}${dose} in the next few days. See you there!`,
            followup: `Hi! We missed you at the health center today. Would you like to reschedule ${child}'s vaccination? Please reply to this message.`
        };

        body.value = messages[template] || messages['followup'];
        document.getElementById('charCount').innerText = "Characters: " + body.value.length;
    }

    function logCall() {
        alert("Call with " + currentChild + "'s guardian logged successfully.");
        // TODO: Implement actual call logging functionality
    }

    // Update character count on typing
    document.getElementById('messageBody').addEventListener('input', function() {
        document.getElementById('charCount').innerText = "Characters: " + this.value.length;
    });

    // Handle SMS form submission
    document.getElementById('smsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert("SMS sent to " + currentChild + "'s guardian!");
        // TODO: Implement actual SMS sending functionality
        // Close modal after sending
        bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
    });
</script>

@endsection