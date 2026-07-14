@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Add Follow-up
        </h4>
        <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>
    <div class="custom-card p-4">
        <div class="card-body">
            <form action="{{ route('admin.followups.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Quotation <span class="text-danger">*</span></label>
                        <select name="quotation_id" class="custom-select select2 @error('quotation_id') is-invalid @enderror" required>
                            <option value="">Select Quotation</option>
                            @foreach($quotations as $quotation)
                                <option value="{{ $quotation->id }}" {{ old('quotation_id') == $quotation->id ? 'selected' : '' }}>{{ $quotation->quotation_number }} - {{ $quotation->customer->company_name ?? '' }}</option>
                            @endforeach
                        </select>
                        @error('quotation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label table-dark-text">Follow-up Date <span class="text-danger">*</span></label>
                        <input type="date" name="follow_up_date" class="custom-input no-icon @error('follow_up_date') is-invalid @enderror" value="{{ old('follow_up_date') }}" required>
                        @error('follow_up_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label table-dark-text">Follow-up Time</label>
                        <input type="time" name="follow_up_time" class="custom-input no-icon @error('follow_up_time') is-invalid @enderror" value="{{ old('follow_up_time') }}">
                        @error('follow_up_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label table-dark-text">Status</label>
                        <select name="status" class="custom-select @error('status') is-invalid @enderror">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label table-dark-text">Notes</label>
                        <textarea name="notes" class="custom-input no-icon @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="pt-4 mt-3 border-top">
                    <button type="submit" class="btn-gradient-primary me-2"><i class="bx bx-save me-1"></i> Save Follow-up</button>
                    <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
});
</script>
@endsection
