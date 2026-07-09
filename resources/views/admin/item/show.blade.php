@extends('admin.layouts.app')
@section('style')
<style>
.detail-label { font-weight: 600; color: #555; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Item Details</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p><span class="detail-label">Name:</span><br>{{ $item->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">SKU:</span><br>{{ $item->sku }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Unit:</span><br>{{ $item->unit ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Rate:</span><br>{{ number_format($item->rate, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Tax Percentage:</span><br>{{ $item->tax_percentage ?? '0' }}%</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Type:</span><br>{{ ucfirst($item->type) }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">HSN Code:</span><br>{{ $item->hsn_code ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Status:</span><br>
                        @if($item->is_active)
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-danger">Inactive</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-12">
                    <p><span class="detail-label">Description:</span><br>{{ $item->description ?? 'N/A' }}</p>
                </div>
            </div>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-primary mt-3"><i class="bx bx-arrow-back"></i> Back to List</a>
        </div>
    </div>
</div>
@endsection
