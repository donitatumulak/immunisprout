@extends('layouts.internal')

@section('title', 'Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* --- Header (Full Width) --- */
    .dynamic-calendar {
        background: #fff;
        border: 1.5px solid var(--orange);
        color: var(--orange);
        padding: 12px 24px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(35, 245, 130, 0.1);
    }

    /* --- Summary Cards --- */
    .summary-card {
        background: var(--white);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .summary-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    .icon-box {
        width: 54px;
        height: 54px;
        background: var(--cream);
        color: var(--orange);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 18px;
        flex-shrink: 0;
    }

    .summary-info .count { 
        font-size: 1.75rem; 
        font-weight: 800; 
        line-height: 1; 
        margin-bottom: 4px;
    }

    .summary-info .label { 
        font-size: 0.7rem; 
        color: var(--text-muted); 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.5px;
    }

    /* --- Content Cards & Graphs --- */
    .content-card {
        background: var(--white);
        border-radius: 20px;
        padding: 25px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
        border: 1px solid #f1f5f9;
        min-height: 450px; /* Taller cards */
        display: flex;
        flex-direction: column;
    }

    .content-card h4 {
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .chart-container {
        position: relative;
        flex-grow: 1;
        width: 100%;
        min-height: 320px;
    }

    /* --- Enhanced Live Alerts --- */
    .alert-pill {
        background: #fbfcfd;
        border: 1px solid #f1f5f9;
        border-left: 5px solid var(--orange);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .alert-pill:hover {
        background: #fff;
        transform: scale(1.01);
        border-color: #e2e8f0;
    }

    .alert-icon-wrapper {
        width: 40px;
        height: 40px;
        background: #fff7ed;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    /* --- Action Buttons --- */
    .btn-custom {
        background-color: var(--green);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        width: 100%;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(45, 106, 79, 0.15);
    }

    .btn-custom:hover {
        background-color: #1b4332;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(45, 106, 79, 0.25);
    }
</style>
<div class="container-fluid" id="main-content">
    <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 mb-4 shadow-sm" 
        style="padding: 2.5rem; border-radius: 20px; background: #fff; border-left: 8px solid var(--green, #198754);">
        
        <div class="d-flex align-items-center">
            <div class="header-icon-bg d-flex align-items-center justify-content-center me-4" 
                style="width: 80px; height: 80px; background: rgba(25, 135, 84, 0.1); color: #198754; border-radius: 18px; font-size: 2rem;">
                <i class="fas fa-user-md"></i>
            </div>
            
            <div>
                <h1 class="mb-1 fw-bold text-dark" style="font-size: 2.2rem;">Welcome, {{ auth()->user()->worker->full_name }}!</h1>
                <p class="text-muted mb-0 d-flex align-items-center" style="font-size: 1.1rem;">
                    <i class="fas fa-map-marker-alt me-2 text-success"></i> 
                    Barangay {{ auth()->user()->worker->address->addr_barangay ?? 'General' }} Health Center
                </p>
            </div>
        </div>

        <div class="dynamic-calendar px-4 py-3 text-center" 
            style="background: #f8f9fa; border-radius: 15px; min-width: 180px; border: 1px solid #e9ecef;">
            <div class="fw-bold text-success" id="liveDate" style="font-size: 1.8rem; letter-spacing: 1px;">
                {{ now()->format('M d') }}
            </div>
            <div id="liveTime" class="text-muted fw-bold" style="font-size: 1rem; text-transform: uppercase;">
                {{ now()->format('H:i:s A') }}
            </div>
        </div>
    </div>

    <div class="px-4">
        {{-- SUMMARY CARDS (Dynamic) --}}
        <div class="row g-3 mb-4">
            @php
                $statCards = [
                    ['icon' => 'fa-children', 'count' => $stats['total_children'], 'label' => 'Total Children'],
                    ['icon' => 'fa-user-plus', 'count' => $stats['newly_registered'], 'label' => 'Newly Registered'],
                    ['icon' => 'fa-calendar-check', 'count' => $stats['upcoming'], 'label' => 'Upcoming'],
                    ['icon' => 'fa-clock', 'count' => $stats['overdue'], 'label' => 'Overdue Cases'],
                    ['icon' => 'fa-check-double', 'count' => $stats['completed'], 'label' => 'Completed']
                ];
            @endphp
            @foreach($statCards as $stat)
            <div class="col-md-4 col-lg">
                <div class="summary-card">
                    <div class="icon-box"><i class="fas {{ $stat['icon'] }}"></i></div>
                    <div class="summary-info">
                        <div class="count text-dark">{{ $stat['count'] }}</div>
                        <div class="label">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-7">
                {{-- LIVE SYSTEM ALERTS (Dynamic) --}}
                <div>
                    <h2><i class="fas fa-bolt me-2 text-warning"></i>Live System Alerts</h2>
                        @forelse($liveAlerts as $alert)
                            <div class="alert-pill">
                                <div class="alert-icon-wrapper">
                                    <i class="fas {{ $alert->notif_type == 'inventory' ? 'fa-box-open text-primary' : ($alert->notif_priority == 'high' ? 'fa-exclamation-triangle text-danger' : 'fa-info-circle text-warning') }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $alert->notif_title }}</div>
                                    <div class="text-muted" style="font-size: 0.9rem;">{{ Str::limit($alert->notif_message, 60) }}</div>
                                </div>
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 0.7rem;">
                                    {{ $alert->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No recent alerts
                            </div>
                        @endforelse
                </div>

                {{-- MONTHLY PERFORMANCE CHART (Dynamic) --}}
                <div class="content-card">
                    <h2><i class="fas fa-chart-line me-2 text-success"></i> Monthly Performance Metrics</h2>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                {{-- QUICK ACTIONS --}}
                <div class="mb-4">
                    <h2 class="px-2"><i class="fas fa-rocket me-2 text-success"></i>Quick Actions</h2>
                     <button class="btn btn-custom" onclick="window.location.href='{{ route('children.index', ['add' => 'true']) }}'">
                        <i class="fas fa-plus-circle"></i> ADD NEW CHILD RECORD
                    </button>
                        <button class="btn btn-custom" style="background-color: var(--orange);" onclick="window.location.href='{{ route('inventory.index') }}'">
                        <i class="fas fa-boxes"></i> MANAGE INVENTORY
                    </button>
                </div>

                {{-- STATUS DISTRIBUTION CHART (Dynamic) --}}
                <div class="content-card">
                    <h2 class="mb-5"><i class="fas fa-pie-chart me-2 text-success"></i>Vaccination Status Distribution</h2>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                {{-- VACCINE PRIORITY LIST (Dynamic) --}}
                <div class="mt-4 px-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-muted">VACCINE PRIORITY</span>
                        @if($vaccinePriorityList->sum('overdue') > 0)
                            <span class="badge bg-soft-danger text-danger" style="font-size: 0.6rem;">ACTION REQUIRED</span>
                        @endif
                    </div>
                    
                    @forelse($vaccinePriorityList as $vaccine)
                        <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-bold">{{ $vaccine['vaccine_name'] }}</h6>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    @if($vaccine['overdue'] > 0)
                                        {{ $vaccine['overdue'] }} Children Overdue
                                    @else
                                        {{ $vaccine['upcoming'] }} Children Upcoming
                                    @endif
                                </div>
                            </div>
                            <div class="progress w-25" style="height: 6px;">
                                @php
                                    $percentage = $vaccine['total'] > 0 ? ($vaccine['total'] / $stats['total_children']) * 100 : 0;
                                    $colorClass = $vaccine['overdue'] > 0 ? 'bg-danger' : 'bg-warning';
                                @endphp
                                <div class="progress-bar {{ $colorClass }}" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0">All vaccinations are up to date!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Live Clock Logic
    function updateClock() {
        const now = new Date();
        document.getElementById('liveDate').innerText = now.toLocaleDateString('en-US', { month: 'short', day: '2-digit' }).toUpperCase();
        document.getElementById('liveTime').innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart.js Configuration
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: { padding: 20, font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' }, usePointStyle: true } 
            } 
        }
    };

    // Performance Chart (Bar) - Dynamic Data from Controller
    const perfCtx = document.getElementById('performanceChart').getContext('2d');
    const greenGrad = perfCtx.createLinearGradient(0, 0, 0, 350);
    greenGrad.addColorStop(0, '#32a776'); // Deep Deep Green
    greenGrad.addColorStop(1, '#95d5b2'); 

    // Get dynamic data from controller
    const monthlyLabels = @json($monthlyPerformance->pluck('month_name'));
    const monthlyData = @json($monthlyPerformance->pluck('total'));

    new Chart(perfCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels.length > 0 ? monthlyLabels : ['No Data'],
            datasets: [{
                label: 'Vaccinations Administered',
                data: monthlyData.length > 0 ? monthlyData : [0],
                backgroundColor: greenGrad,
                borderRadius: 12,
                barThickness: 40
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Status Chart (Doughnut) - Dynamic Data from Controller
    const statusCtx = document.getElementById('statusChart').getContext('2d');

    // Create Gradients
    const statusGreen = statusCtx.createLinearGradient(0, 0, 0, 300);
    statusGreen.addColorStop(0, '#32a776');
    statusGreen.addColorStop(1, '#95d5b2');

    const statusAmber = statusCtx.createLinearGradient(0, 0, 0, 300);
    statusAmber.addColorStop(0, '#ffa500');
    statusAmber.addColorStop(1, '#ffdb99');

    const statusRed = statusCtx.createLinearGradient(0, 0, 0, 300);
    statusRed.addColorStop(0, '#f43f5e');
    statusRed.addColorStop(1, '#fda4af');

    // Get dynamic data from controller
    const statusData = @json($statusDistribution);

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Upcoming', 'Overdue'],
            datasets: [{
                data: [statusData.completed, statusData.upcoming, statusData.overdue],
                backgroundColor: [statusGreen, statusAmber, statusRed],
                hoverOffset: 15,
                borderWidth: 0,
                borderRadius: 10,
                spacing: 4
            }]
        },
        options: {
            ...chartOptions,
            cutout: '65%',
        }
    });
</script>

@endsection