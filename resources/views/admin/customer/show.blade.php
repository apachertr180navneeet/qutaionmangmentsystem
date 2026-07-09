@extends('admin.layouts.app')
@section('style')
<style>
.detail-label { font-weight: 600; color: #555; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Customer Details</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p><span class="detail-label">Company Name:</span><br>{{ $customer->company_name }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Contact Person:</span><br>{{ $customer->contact_person }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Email:</span><br>{{ $customer->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Phone:</span><br>{{ $customer->phone }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Alternate Phone:</span><br>{{ $customer->alt_phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">GST Number:</span><br>{{ $customer->gst_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">City:</span><br>{{ $customer->city ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">State:</span><br>{{ $customer->state ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Zip Code:</span><br>{{ $customer->zip_code ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Country:</span><br>{{ $customer->country ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Billing Address:</span><br>{{ $customer->billing_address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Shipping Address:</span><br>{{ $customer->shipping_address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12">
                    <p><span class="detail-label">Notes:</span><br>{{ $customer->notes ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Status:</span><br>
                        @if($customer->status)
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-danger">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary mt-3"><i class="bx bx-arrow-back"></i> Back to List</a>
        </div>
    </div>
</div>
@endsection
