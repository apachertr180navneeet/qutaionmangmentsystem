@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Add New Item
        </h4>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
    <div class="custom-card p-4">
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Item Image</label>
                        <input type="file" name="image" class="custom-input no-icon @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="custom-input no-icon @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="custom-input no-icon @error('sku') is-invalid @enderror" value="{{ old('sku') }}" required>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Unit</label>
                        <input type="text" name="unit" class="custom-input no-icon @error('unit') is-invalid @enderror" value="{{ old('unit') }}" placeholder="e.g. Pcs, Kg, Box">
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Rate <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="rate" class="custom-input no-icon @error('rate') is-invalid @enderror" value="{{ old('rate') }}" required>
                        @error('rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">HSN Code</label>
                        <input type="text" name="hsn_code" class="custom-input no-icon @error('hsn_code') is-invalid @enderror" value="{{ old('hsn_code') }}">
                        @error('hsn_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Description</label>
                        <textarea name="description" class="custom-input no-icon @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4 mt-3 border-top">
                    <button type="submit" class="btn-gradient-primary me-2"><i class="bx bx-save me-1"></i> Save Item</button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
