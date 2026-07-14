@extends('admin.layouts.app')
@section('style')
<style>
.logo-preview { max-height: 80px; margin-top: 8px; border: 1px solid #ddd; padding: 4px; border-radius: 4px; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Company Settings
        </h4>
    </div>
    <div class="custom-card p-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="custom-input no-icon @error('company_name') is-invalid @enderror" value="{{ old('company_name', $setting->company_name ?? '') }}" required>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Email</label>
                        <input type="email" name="email" class="custom-input no-icon @error('email') is-invalid @enderror" value="{{ old('email', $setting->email ?? '') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Phone</label>
                        <input type="text" name="phone" class="custom-input no-icon @error('phone') is-invalid @enderror" value="{{ old('phone', $setting->phone ?? '') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Address</label>
                        <input type="text" name="address" class="custom-input no-icon @error('address') is-invalid @enderror" value="{{ old('address', $setting->address ?? '') }}">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">City</label>
                        <input type="text" name="city" class="custom-input no-icon @error('city') is-invalid @enderror" value="{{ old('city', $setting->city ?? '') }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">State</label>
                        <input type="text" name="state" class="custom-input no-icon @error('state') is-invalid @enderror" value="{{ old('state', $setting->state ?? '') }}">
                        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Zip Code</label>
                        <input type="text" name="zip_code" class="custom-input no-icon @error('zip_code') is-invalid @enderror" value="{{ old('zip_code', $setting->zip_code ?? '') }}">
                        @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Country</label>
                        <input type="text" name="country" class="custom-input no-icon @error('country') is-invalid @enderror" value="{{ old('country', $setting->country ?? '') }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">GST Number</label>
                        <input type="text" name="gst_number" class="custom-input no-icon @error('gst_number') is-invalid @enderror" value="{{ old('gst_number', $setting->gst_number ?? '') }}">
                        @error('gst_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">PAN Number</label>
                        <input type="text" name="pan_number" class="custom-input no-icon @error('pan_number') is-invalid @enderror" value="{{ old('pan_number', $setting->pan_number ?? '') }}">
                        @error('pan_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Company Logo</label>
                        <input type="file" name="logo" class="custom-input no-icon @error('logo') is-invalid @enderror" accept="image/*" onchange="document.getElementById('logoPreview').src = window.URL.createObjectURL(this.files[0])">
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if(!empty($setting->logo))
                            <img src="{{ asset($setting->logo) }}" class="logo-preview mt-2" id="logoPreview" style="border-radius: 8px;">
                        @else
                            <img src="" class="logo-preview mt-2" id="logoPreview" style="display:none; border-radius: 8px;">
                        @endif
                    </div>
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Terms & Conditions</label>
                        <textarea name="terms_conditions" class="custom-input no-icon @error('terms_conditions') is-invalid @enderror" rows="4">{{ old('terms_conditions', $setting->terms_conditions ?? '') }}</textarea>
                        @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Signature</label>
                        <textarea name="signature" class="custom-input no-icon @error('signature') is-invalid @enderror" rows="3">{{ old('signature', $setting->signature ?? '') }}</textarea>
                        @error('signature') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4 mt-3 border-top">
                    <button type="submit" class="btn-gradient-primary"><i class="bx bx-save me-1"></i> Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
