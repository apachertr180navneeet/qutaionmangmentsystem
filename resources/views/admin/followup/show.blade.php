@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Follow-up Details</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.followups.edit', $followUp->id) }}" class="btn-gradient-primary">
                <i class="bx bx-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="custom-card">
        <div class="row g-4 p-4">
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Quotation</h6>
                <p class="fw-semibold">{{ $followUp->quotation->quotation_number ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Customer</h6>
                <p class="fw-semibold">{{ $followUp->quotation->customer->company_name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Follow-up Date</h6>
                <p class="fw-semibold">{{ $followUp->follow_up_date ? date('d-m-Y', strtotime($followUp->follow_up_date)) : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Follow-up Time</h6>
                <p class="fw-semibold">{{ $followUp->follow_up_time ? date('h:i A', strtotime($followUp->follow_up_time)) : 'All Day' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Status</h6>
                <p class="fw-semibold">{{ ucfirst($followUp->status) }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Assigned To</h6>
                <p class="fw-semibold">{{ $followUp->user->full_name ?? 'N/A' }}</p>
            </div>
            <div class="col-12">
                <h6 class="text-muted mb-1">Notes</h6>
                <p class="fw-semibold">{{ $followUp->notes ?: 'No notes' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
