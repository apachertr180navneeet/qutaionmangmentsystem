@extends('admin.layouts.app')
@section('style')
<style>
.logo-preview { max-height: 80px; margin-top: 8px; border: 1px solid #ddd; padding: 4px; border-radius: 4px; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Company Settings</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $setting->company_name ?? '') }}" required>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $setting->email ?? '') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $setting->phone ?? '') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $setting->address ?? '') }}">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $setting->city ?? '') }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $setting->state ?? '') }}">
                        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code', $setting->zip_code ?? '') }}">
                        @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $setting->country ?? '') }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number', $setting->gst_number ?? '') }}">
                        @error('gst_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" class="form-control @error('pan_number') is-invalid @enderror" value="{{ old('pan_number', $setting->pan_number ?? '') }}">
                        @error('pan_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Logo</label>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*" onchange="document.getElementById('logoPreview').src = window.URL.createObjectURL(this.files[0])">
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if(!empty($setting->logo))
                            <img src="{{ asset($setting->logo) }}" class="logo-preview" id="logoPreview">
                        @else
                            <img src="" class="logo-preview" id="logoPreview" style="display:none;">
                        @endif
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Terms & Conditions</label>
                        <textarea name="terms_conditions" class="form-control @error('terms_conditions') is-invalid @enderror" rows="4">{{ old('terms_conditions', $setting->terms_conditions ?? '') }}</textarea>
                        @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Signature</label>
                        <textarea name="signature" class="form-control @error('signature') is-invalid @enderror" rows="3">{{ old('signature', $setting->signature ?? '') }}</textarea>
                        @error('signature') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
