@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-primary">Reports</span> Dashboard</h4>
            <p class="text-muted mb-0">Generate, view, print, and export system reports in PDF and Excel formats.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100 shadow-sm border-0 transition-hover">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar avatar-md mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 12px; background: rgba(142, 45, 226, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-group bx-md text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-2">Customer Wise Report</h5>
                        <p class="card-text text-muted">View all quotations grouped by specific customer with totals, PDF export & print.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.customer_wise') }}" class="btn btn-primary w-100">
                            <i class="bx bx-file me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100 shadow-sm border-0 transition-hover">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar avatar-md mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 12px; background: rgba(40, 167, 69, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-calendar bx-md text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-2">Date Wise Report</h5>
                        <p class="card-text text-muted">Filter and analyze quotations within customized date ranges with instant PDF & print.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.date_wise') }}" class="btn btn-success w-100">
                            <i class="bx bx-file me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100 shadow-sm border-0 transition-hover">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar avatar-md mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 12px; background: rgba(255, 171, 0, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-check-shield bx-md text-warning"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-2">Status Wise Report</h5>
                        <p class="card-text text-muted">Track quotations by status (Draft, Sent, Approved, Expired, Rejected) with summaries.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.status_wise') }}" class="btn btn-warning w-100 text-white">
                            <i class="bx bx-file me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100 shadow-sm border-0 transition-hover">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar avatar-md mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 12px; background: rgba(13, 202, 240, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-line-chart bx-md text-info"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-2">Monthly Report</h5>
                        <p class="card-text text-muted">Examine monthly performance, financial sums, and quotation progress over time.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.monthly') }}" class="btn btn-info w-100 text-white">
                            <i class="bx bx-file me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100 shadow-sm border-0 transition-hover">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="avatar avatar-md mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 12px; background: rgba(220, 53, 69, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bx bx-package bx-md text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-2">Item Wise Report</h5>
                        <p class="card-text text-muted">Monitor item sales volume, quantities, rates, and totals across all quotations.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.item_wise') }}" class="btn btn-danger w-100">
                            <i class="bx bx-file me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
