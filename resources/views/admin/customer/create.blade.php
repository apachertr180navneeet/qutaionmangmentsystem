@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Add New Customer
        </h4>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
    <div class="custom-card p-4">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="custom-input no-icon @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person" class="custom-input no-icon @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" required>
                        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="custom-input no-icon @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="custom-input no-icon @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Alternate Phone</label>
                        <input type="text" name="alt_phone" class="custom-input no-icon @error('alt_phone') is-invalid @enderror" value="{{ old('alt_phone') }}">
                        @error('alt_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">GST Number</label>
                        <input type="text" name="gst_number" class="custom-input no-icon @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}">
                        @error('gst_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">City</label>
                        <input type="text" name="city" class="custom-input no-icon @error('city') is-invalid @enderror" value="{{ old('city') }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">State</label>
                        <input type="text" name="state" class="custom-input no-icon @error('state') is-invalid @enderror" value="{{ old('state') }}">
                        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Zip Code</label>
                        <input type="text" name="zip_code" class="custom-input no-icon @error('zip_code') is-invalid @enderror" value="{{ old('zip_code') }}">
                        @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Country</label>
                        <input type="text" name="country" class="custom-input no-icon @error('country') is-invalid @enderror" value="{{ old('country') }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Billing Address</label>
                        <textarea name="billing_address" class="custom-input no-icon @error('billing_address') is-invalid @enderror" rows="3">{{ old('billing_address') }}</textarea>
                        @error('billing_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Shipping Address</label>
                        <textarea name="shipping_address" class="custom-input no-icon @error('shipping_address') is-invalid @enderror" rows="3">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Notes</label>
                        <textarea name="notes" class="custom-input no-icon @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status') ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4 mt-3 border-top">
                    <button type="submit" class="btn-gradient-primary me-2"><i class="bx bx-save me-1"></i> Save Customer</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
