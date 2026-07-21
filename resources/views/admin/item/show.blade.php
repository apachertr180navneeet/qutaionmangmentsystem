@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Item Details
        </h4>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
    <div class="custom-card p-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-12 mb-3">
                    @if($item->image)
                        <img src="{{ $item->image }}" alt="Item Image" class="img-thumbnail" style="max-height: 200px; border-radius: 12px;">
                    @else
                        <div class="text-muted p-4 border rounded text-center bg-light" style="max-width: 200px; border-radius: 12px;">No Image Available</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Name:</span><span class="text-muted">{{ $item->name }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">SKU:</span><span class="text-muted">{{ $item->sku }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Unit:</span><span class="text-muted">{{ $item->unit ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Rate:</span><span class="text-muted">{{ number_format($item->rate, 2) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Status:</span>
                        @if($item->is_active)
                            <span class="badge-custom badge-active">ACTIVE</span>
                        @else
                            <span class="badge-custom badge-inactive">INACTIVE</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-12">
                    <p><span class="table-dark-text d-block mb-1">Description:</span><span class="text-muted">{{ $item->description ?? 'N/A' }}</span></p>
                </div>
            </div>
            <div class="pt-4 mt-3 border-top">
                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;"><i class="bx bx-arrow-back me-1"></i> Back to List</a>
                <a href="{{ route('admin.items.edit', $item->id) }}" class="btn-gradient-primary ms-2"><i class="bx bx-edit me-1"></i> Edit Item</a>
            </div>
        </div>
    </div>
</div>
@endsection
