@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Reports</span>
    </h5>
    <div class="row g-4">
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bx bx-group bx-lg text-primary mb-3"></i>
                    <h5 class="card-title">Customer Wise</h5>
                    <p class="card-text text-muted">View quotations grouped by customer.</p>
                    <a href="{{ route('admin.reports.customer_wise') }}" class="btn btn-outline-primary">Generate Report</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bx bx-calendar bx-lg text-success mb-3"></i>
                    <h5 class="card-title">Date Wise</h5>
                    <p class="card-text text-muted">View quotations within a date range.</p>
                    <a href="{{ route('admin.reports.date_wise') }}" class="btn btn-outline-success">Generate Report</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bx bx-check-shield bx-lg text-warning mb-3"></i>
                    <h5 class="card-title">Status Wise</h5>
                    <p class="card-text text-muted">View quotations filtered by status.</p>
                    <a href="{{ route('admin.reports.status_wise') }}" class="btn btn-outline-warning">Generate Report</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bx bx-line-chart bx-lg text-info mb-3"></i>
                    <h5 class="card-title">Monthly Report</h5>
                    <p class="card-text text-muted">View monthly quotation summaries.</p>
                    <a href="{{ route('admin.reports.monthly') }}" class="btn btn-outline-info">Generate Report</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bx bx-package bx-lg text-danger mb-3"></i>
                    <h5 class="card-title">Item Wise</h5>
                    <p class="card-text text-muted">View quotations by specific item.</p>
                    <a href="{{ route('admin.reports.item_wise') }}" class="btn btn-outline-danger">Generate Report</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
