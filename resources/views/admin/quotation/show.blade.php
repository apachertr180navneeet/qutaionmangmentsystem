@extends('admin.layouts.app')
@section('style')
<style>
    /* ── Quotation Show Page ── */
    .q-show-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }
    .q-show-header .title-area {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .q-show-header .title-area h4 {
        margin: 0;
        font-weight: 700;
        background: linear-gradient(45deg, #8E2DE2, #4A00E0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .q-show-header .btn-group-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .q-show-header .btn-group-actions .btn {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .q-show-header .btn-group-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .btn-back-list {
        background: #fff;
        color: #4A00E0;
        border: 1.5px solid #d4c5f9;
    }
    .btn-back-list:hover {
        background: #f3eefe;
        color: #4A00E0;
        border-color: #8E2DE2;
    }
    .btn-edit-q {
        background: linear-gradient(135deg, #8E2DE2, #4A00E0);
        color: #fff;
        border: none;
    }
    .btn-edit-q:hover {
        background: linear-gradient(135deg, #7B27C1, #3D00B8);
        color: #fff;
    }
    .btn-download-q {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: #fff;
        border: none;
    }
    .btn-download-q:hover {
        background: linear-gradient(135deg, #1e8e3e, #17a589);
        color: #fff;
    }

    /* ── Info Card ── */
    .q-info-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 20px rgba(78, 0, 224, 0.06);
        overflow: hidden;
        animation: fadeSlideUp 0.4s ease;
    }
    .q-info-card .card-header-gradient {
        background: linear-gradient(135deg, #8E2DE2, #4A00E0);
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .q-info-card .card-header-gradient .q-number {
        font-size: 1.15rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
    }
    .q-info-card .card-header-gradient .q-number i {
        margin-right: 8px;
        font-size: 1.25rem;
    }
    .q-status-badge {
        padding: 5px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .status-draft { background-color: rgba(255,255,255,0.2) !important; color: #fff !important; border: 1px solid rgba(255,255,255,0.4) !important; }
    .status-sent { background-color: #e8f4fd !important; color: #0ea5e9 !important; border: 1px solid #e8f4fd !important; }
    .status-approved { background-color: #dcfce7 !important; color: #16a34a !important; border: 1px solid #dcfce7 !important; }
    .status-expired { background-color: #fef3cd !important; color: #d97706 !important; border: 1px solid #fef3cd !important; }
    .status-rejected { background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fee2e2 !important; }
    .q-status-badge:focus { box-shadow: none !important; background-color: #fff !important; color: #212529 !important; }
    .q-status-badge:disabled { opacity: 0.8; cursor: not-allowed !important; }

    .q-info-body {
        padding: 24px;
    }
    .q-detail-item {
        text-align: center;
        padding: 16px 12px;
        border-radius: 10px;
        background: #f8f7ff;
        transition: all 0.25s ease;
    }
    .q-detail-item:hover {
        background: #eee8ff;
        transform: translateY(-2px);
    }
    .q-detail-item .label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #8E2DE2;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }
    .q-detail-item .value {
        font-size: 1rem;
        font-weight: 600;
        color: #2d2d3f;
    }

    /* ── Items Table Card ── */
    .q-items-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 20px rgba(78, 0, 224, 0.06);
        overflow: hidden;
        animation: fadeSlideUp 0.5s ease;
    }
    .q-items-card .card-header {
        background: #fff;
        border-bottom: 2px solid #f0ebff;
        padding: 18px 24px;
    }
    .q-items-card .card-header strong {
        font-size: 1.05rem;
        color: #2d2d3f;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .q-items-card .card-header strong i {
        color: #8E2DE2;
        font-size: 1.2rem;
    }
    .q-items-card .table thead {
        background: linear-gradient(135deg, #f8f7ff 0%, #eee8ff 100%);
    }
    .q-items-card .table thead th {
        font-weight: 700;
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c5ce7;
        border-bottom: none;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .q-items-card .table tbody td {
        font-size: 0.875rem;
        color: #444;
        padding: 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f5f3ff;
    }
    .q-items-card .table tbody tr:hover {
        background: #faf8ff;
    }
    .item-name {
        font-weight: 600;
        color: #2d2d3f;
    }
    .item-total {
        font-weight: 700;
        color: #8E2DE2;
    }
    .item-thumb {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #f0ebff;
        flex-shrink: 0;
    }
    .item-thumb-placeholder {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f7ff, #eee8ff);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #8E2DE2;
        font-size: 1.2rem;
    }

    /* ── Summary Card ── */
    .q-summary-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 20px rgba(78, 0, 224, 0.06);
        overflow: hidden;
        animation: fadeSlideUp 0.6s ease;
    }
    .q-summary-card .card-header {
        background: #fff;
        border-bottom: 2px solid #f0ebff;
        padding: 18px 24px;
    }
    .q-summary-card .card-header strong {
        font-size: 1.05rem;
        color: #2d2d3f;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .q-summary-card .card-header strong i {
        color: #8E2DE2;
        font-size: 1.2rem;
    }
    .summary-table {
        border: none !important;
    }
    .summary-table td {
        padding: 12px 18px !important;
        border: none !important;
        border-bottom: 1px solid #f5f3ff !important;
        font-size: 0.9rem;
    }
    .summary-table .summary-label {
        color: #666;
        font-weight: 500;
    }
    .summary-table .summary-value {
        font-weight: 600;
        color: #2d2d3f;
    }
    .summary-table .discount-value {
        color: #dc2626;
        font-weight: 600;
    }
    .summary-table .tax-value {
        color: #d97706;
        font-weight: 600;
    }
    .grand-total-row {
        background: linear-gradient(135deg, #f8f7ff, #eee8ff) !important;
    }
    .grand-total-row td {
        border-bottom: none !important;
        padding: 16px 18px !important;
    }
    .grand-total-label {
        font-weight: 700 !important;
        font-size: 1rem !important;
        color: #4A00E0 !important;
    }
    .grand-total-value {
        font-weight: 800 !important;
        font-size: 1.15rem !important;
        color: #8E2DE2 !important;
    }

    .notes-section {
        padding: 20px 24px;
        background: #faf8ff;
        border-radius: 10px;
    }
    .notes-section .notes-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8E2DE2;
        margin-bottom: 8px;
    }
    .notes-section .notes-text {
        color: #555;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- ── Header with Actions ── --}}
    <div class="q-show-header">
        <div class="title-area">
            <h4><i class="bx bx-file-find"></i> Quotation Details</h4>
        </div>
        <div class="btn-group-actions">
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-back-list">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
            <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn btn-edit-q">
                <i class="bx bx-edit me-1"></i> Edit Quotation
            </a>
            <a href="{{ route('admin.quotations.pdf', $quotation->id) }}" class="btn btn-download-q" target="_blank">
                <i class="bx bx-file-blank me-1"></i> Generate PDF
            </a>
        </div>
    </div>

    {{-- ── Quotation Info Card ── --}}
    <div class="card q-info-card mb-4">
        <div class="card-header-gradient">
            <span class="q-number"><i class="bx bx-receipt"></i> {{ $quotation->quotation_number }}</span>
            @php
                $statusMap = [
                    'draft'    => 'status-draft',
                    'sent'     => 'status-sent',
                    'approved' => 'status-approved',
                    'expired'  => 'status-expired',
                    'rejected' => 'status-rejected',
                ];
            @endphp
            <div class="d-flex align-items-center gap-2">
                <span class="text-white" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">STATUS:</span>
                <select id="statusAjaxSelect" class="form-select form-select-sm q-status-badge {{ $statusMap[$quotation->status] ?? 'status-draft' }} fw-bold" style="cursor: pointer; width: 140px;" data-id="{{ $quotation->id }}" {{ in_array($quotation->status, ['approved', 'rejected', 'expired']) ? 'disabled' : '' }}>
                    <option value="draft" class="text-dark bg-white" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" class="text-dark bg-white" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" class="text-dark bg-white" {{ $quotation->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" class="text-dark bg-white" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" class="text-dark bg-white" {{ $quotation->status == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
        </div>
        <div class="q-info-body">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="q-detail-item">
                        <div class="label"><i class="bx bx-user"></i> Customer</div>
                        <div class="value">{{ $quotation->customer->company_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="q-detail-item">
                        <div class="label"><i class="bx bx-calendar"></i> Date</div>
                        <div class="value">{{ $quotation->created_at ? date('d M Y', strtotime($quotation->created_at)) : 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="q-detail-item">
                        <div class="label"><i class="bx bx-calendar-check"></i> Valid Until</div>
                        <div class="value">{{ $quotation->valid_until ? date('d M Y', strtotime($quotation->valid_until)) : 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="q-detail-item">
                        <div class="label"><i class="bx bx-money"></i> Grand Total</div>
                        <div class="value" style="color: #8E2DE2; font-size: 1.1rem;">₹{{ number_format($quotation->grand_total, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Items Table ── --}}
    <div class="card q-items-card mb-4">
        <div class="card-header">
            <strong><i class="bx bx-list-ul"></i> Line Items</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Description</th>
                            <th>HSN Code</th>
                            <th>Unit</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->items as $key => $item)
                        <tr>
                            <td class="text-muted">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->item && $item->item->image)
                                    <img src="{{ $item->item->image }}" alt="{{ $item->item->name }}" class="item-thumb">
                                    @else
                                    <div class="item-thumb-placeholder">
                                        <i class="bx bx-package"></i>
                                    </div>
                                    @endif
                                    <span class="item-name">{{ $item->item->name ?? $item->item_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $item->item->description ?? '—' }}</td>
                            <td>{{ $item->item->hsn_code ?? '—' }}</td>
                            <td>{{ $item->item->unit ?? '—' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-end item-total">{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bx bx-package" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No items added yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Summary Card ── --}}
    <div class="card q-summary-card mb-4">
        <div class="card-header">
            <strong><i class="bx bx-calculator"></i> Summary</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    @if($quotation->notes)
                    <div class="notes-section mb-3">
                        <div class="notes-title"><i class="bx bx-note"></i> Notes</div>
                        <p class="notes-text">{{ $quotation->notes }}</p>
                    </div>
                    @endif
                    @if($quotation->terms_conditions)
                    <div class="notes-section">
                        <div class="notes-title"><i class="bx bx-file"></i> Terms & Conditions</div>
                        <p class="notes-text">{{ $quotation->terms_conditions }}</p>
                    </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <table class="table summary-table">
                        <tr>
                            <td class="summary-label">Subtotal</td>
                            <td class="text-end summary-value">{{ number_format($quotation->subtotal, 2) }}</td>
                        </tr>
                        @if($quotation->discount_amount > 0)
                        <tr>
                            <td class="summary-label">
                                Discount
                                <span class="text-muted">({{ $quotation->discount_type == 'percentage' ? $quotation->discount_value.'%' : 'Fixed' }})</span>
                            </td>
                            <td class="text-end discount-value">-{{ number_format($quotation->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="summary-label">Total Tax</td>
                            <td class="text-end tax-value">{{ number_format($quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Round Off</td>
                            <td class="text-end summary-value">{{ number_format($quotation->round_off, 2) }}</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td class="grand-total-label">Grand Total</td>
                            <td class="text-end grand-total-value">₹{{ number_format($quotation->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#statusAjaxSelect').on('change', function() {
        var $select = $(this);
        var status = $select.val();
        var id = $select.data('id');
        var originalClass = $select.attr('class');
        
        // Remove old status classes
        $select.removeClass('status-draft status-sent status-approved status-expired status-rejected');
        
        // Add new class based on selection
        var newClass = 'status-' + status;
        $select.addClass(newClass);

        $.ajax({
            url: "{{ url('admin/quotations') }}/" + id + "/status",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function(response) {
                if(response.success) {
                    if (['approved', 'rejected', 'expired'].includes(status)) {
                        $select.prop('disabled', true);
                    }
                    // Optional: show a small toast notification here
                    console.log(response.message);
                } else {
                    alert('Failed to update status.');
                    // Revert UI on failure
                    $select.attr('class', originalClass);
                    $select.val($select.find('option[selected]').val());
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                // Revert UI on failure
                $select.attr('class', originalClass);
                $select.val($select.find('option[selected]').val());
            }
        });
    });
});
</script>
@endsection
