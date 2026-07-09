@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Add New Item</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Item Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" required>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" placeholder="e.g. Pcs, Kg, Box">
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rate <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="rate" class="form-control @error('rate') is-invalid @enderror" value="{{ old('rate') }}" required>
                        @error('rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">HSN Code</label>
                        <input type="text" name="hsn_code" class="form-control @error('hsn_code') is-invalid @enderror" value="{{ old('hsn_code') }}">
                        @error('hsn_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save Item</button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
