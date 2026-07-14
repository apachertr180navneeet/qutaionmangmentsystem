@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y dashboard-animated">
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card premium-dashboard-card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome to Admin Dashboard! 🎉</h5>
                            <p class="mb-4">
                                You have successfully logged into the Quotation Management System. Here you can manage your quotes, users, and overall system settings.
                            </p>
                            <a href="{{ route('admin.quotations.create') }}" class="btn btn-sm btn-outline-light mt-2">Create Quotation</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('assets/admin/img/illustrations/man-with-laptop-light.png')}}" height="140" alt="View Badge User" class="floating-img" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card hover-card">
                        <div class="card-body text-center">
                            <i class='bx bx-user bx-md text-primary mb-3'></i>
                            <span class="fw-semibold d-block mb-1">Total Customers</span>
                            <h3 class="card-title mb-2">{{ $totalCustomers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card hover-card">
                        <div class="card-body text-center">
                            <i class='bx bx-box bx-md text-success mb-3'></i>
                            <span class="fw-semibold d-block mb-1">Total Items</span>
                            <h3 class="card-title mb-2">{{ $totalItems ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Total Quotations</small>
                        <h4 class="mb-0">{{ $totalQuotations ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-edit bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Draft</small>
                        <h4 class="mb-0">{{ $draftCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-send bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Sent</small>
                        <h4 class="mb-0">{{ $sentCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-circle bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Approved</small>
                        <h4 class="mb-0">{{ $approvedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Expired</small>
                        <h4 class="mb-0">{{ $expiredCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card hover-card">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x-circle bx-lg"></i></span>
                    </div>
                    <div>
                        <small class="text-muted">Rejected</small>
                        <h4 class="mb-0">{{ $rejectedCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Monthly Quotation Trend</strong></div>
                <div class="card-body">
                    <div id="monthlyTrendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>Status Distribution</strong></div>
                <div class="card-body">
                    <div id="statusPieChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <strong>Recent Quotations</strong>
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Quotation No</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentQuotations ?? [] as $key => $q)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><a href="{{ route('admin.quotations.show', $q->id) }}">{{ $q->quotation_number }}</a></td>
                                    <td>{{ $q->customer->company_name ?? 'N/A' }}</td>
                                    <td>{{ number_format($q->grand_total, 2) }}</td>
                                    @php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                                    <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No quotations yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header"><strong>Today's Follow-ups</strong></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($todayFollowups ?? [] as $followup)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <small class="d-block">{{ $followup->quotation->customer->company_name ?? 'N/A' }}</small>
                                <small class="text-muted">{{ $followup->follow_up_time ? date('h:i A', strtotime($followup->follow_up_time)) : 'All Day' }}</small>
                            </div>
                            <span class="badge bg-label-{{ $followup->status == 'pending' ? 'warning' : 'success' }}">{{ ucfirst($followup->status) }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-3">No follow-ups today.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.followups.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header"><strong>Latest Customers</strong></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($latestCustomers ?? [] as $customer)
                        <li class="list-group-item">
                            <strong>{{ $customer->company_name }}</strong>
                            <small class="d-block text-muted">{{ $customer->email }}</small>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-3">No customers yet.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    var trendOptions = {
        series: [{
            name: 'Quotations',
            data: {!! json_encode($monthlyTrend ?? []) !!}
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false }
        },
        colors: ['#A05AFF'],
        xaxis: {
            categories: {!! json_encode($monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) !!}
        },
        yaxis: {
            title: { text: 'Count' }
        },
        stroke: { curve: 'smooth' }
    };
    var trendChart = new ApexCharts(document.querySelector("#monthlyTrendChart"), trendOptions);
    trendChart.render();

    var statusOptions = {
        series: {!! json_encode($statusDistribution ?? [0,0,0,0,0]) !!},
        chart: {
            type: 'pie',
            height: 300
        },
        labels: ['Draft', 'Sent', 'Approved', 'Expired', 'Rejected'],
        colors: ['#8592a3', '#A05AFF', '#1BCFB4', '#ffab00', '#FE9496'],
        legend: { position: 'bottom' },
        responsive: [{
            breakpoint: 480,
            options: { chart: { width: 200 }, legend: { position: 'bottom' } }
        }]
    };
    var statusChart = new ApexCharts(document.querySelector("#statusPieChart"), statusOptions);
    statusChart.render();
});
</script>
@endsection
