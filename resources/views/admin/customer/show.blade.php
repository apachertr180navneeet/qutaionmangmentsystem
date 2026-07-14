@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Customer Details
        </h4>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
    <div class="custom-card p-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Company Name:</span><span class="text-muted">{{ $customer->company_name }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Contact Person:</span><span class="text-muted">{{ $customer->contact_person }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Email:</span><span class="text-muted">{{ $customer->email }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Phone:</span><span class="text-muted">{{ $customer->phone }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Alternate Phone:</span><span class="text-muted">{{ $customer->alt_phone ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">GST Number:</span><span class="text-muted">{{ $customer->gst_number ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">City:</span><span class="text-muted">{{ $customer->city ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">State:</span><span class="text-muted">{{ $customer->state ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Zip Code:</span><span class="text-muted">{{ $customer->zip_code ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Country:</span><span class="text-muted">{{ $customer->country ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Billing Address:</span><span class="text-muted">{{ $customer->billing_address ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Shipping Address:</span><span class="text-muted">{{ $customer->shipping_address ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-12">
                    <p><span class="table-dark-text d-block mb-1">Notes:</span><span class="text-muted">{{ $customer->notes ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><span class="table-dark-text d-block mb-1">Status:</span>
                        @if($customer->status)
                            <span class="badge-custom badge-active">ACTIVE</span>
                        @else
                            <span class="badge-custom badge-inactive">INACTIVE</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="pt-4 mt-3 border-top">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;"><i class="bx bx-arrow-back me-1"></i> Back to List</a>
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-gradient-primary ms-2"><i class="bx bx-edit me-1"></i> Edit Customer</a>
            </div>
        </div>
    </div>
</div>
@endsection
