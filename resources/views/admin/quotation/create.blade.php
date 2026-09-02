@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* ── Create Quotation Page ── */
    .q-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }
    .q-edit-header h4 {
        margin: 0;
        font-weight: 700;
        background: linear-gradient(45deg, #8E2DE2, #4A00E0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .q-edit-header .btn-group-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .q-edit-header .btn-group-actions .btn {
        border-radius: 8px;
        padding: 8px 22px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .q-edit-header .btn-group-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .btn-cancel-q {
        background: #fff;
        color: #4A00E0;
        border: 1.5px solid #d4c5f9;
    }
    .btn-cancel-q:hover {
        background: #f3eefe;
        color: #4A00E0;
        border-color: #8E2DE2;
    }
    .btn-save-q {
        background: linear-gradient(135deg, #8E2DE2, #4A00E0);
        color: #fff;
        border: none;
    }
    .btn-save-q:hover {
        background: linear-gradient(135deg, #7B27C1, #3D00B8);
        color: #fff;
    }

    /* ── Cards ── */
    .q-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 20px rgba(78, 0, 224, 0.06);
        overflow: hidden;
        animation: fadeSlideUp 0.4s ease;
    }
    .q-card .card-header {
        background: #fff;
        border-bottom: 2px solid #f0ebff;
        padding: 16px 24px;
    }
    .q-card .card-header strong {
        font-size: 1.05rem;
        color: #2d2d3f;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .q-card .card-header strong i {
        color: #8E2DE2;
        font-size: 1.2rem;
    }
    .q-card .card-body {
        padding: 24px;
    }

    /* ── Form Styles ── */
    .form-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c5ce7;
        margin-bottom: 6px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #8E2DE2;
        box-shadow: 0 0 0 0.15rem rgba(142, 45, 226, 0.15);
    }

    /* Hide number input spinners (increase/decrease arrows) */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    /* ── Items Table ── */
    .q-items-card { animation-delay: 0.1s; }
    .q-items-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-add-item {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.25s ease;
    }
    .btn-add-item:hover {
        background: linear-gradient(135deg, #1e8e3e, #17a589);
        color: #fff;
        transform: translateY(-1px);
    }
    .items-table thead {
        background: linear-gradient(135deg, #f8f7ff 0%, #eee8ff 100%);
    }
    .items-table thead th {
        font-weight: 700;
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c5ce7;
        border-bottom: none;
        padding: 12px 10px;
        white-space: nowrap;
    }
    .items-table tbody td {
        vertical-align: middle;
        padding: 10px 8px;
        border-bottom: 1px solid #f5f3ff;
    }
    .items-table tbody tr:hover {
        background: #faf8ff;
    }
    .item-row .form-control, .item-row .form-select {
        font-size: 0.88rem;
        height: 38px;
        border-radius: 8px;
        padding: 6px 10px;
    }
    .items-table .select2-container {
        width: 100% !important;
    }
    .items-table .select2-selection--single {
        min-height: 38px !important;
        height: 38px !important;
        padding: 4px 8px !important;
        border: 1px solid #e8e5f0 !important;
        border-radius: 8px !important;
    }
    .items-table .select2-selection__rendered {
        padding-left: 0 !important;
        padding-right: 20px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 28px !important;
        font-size: 0.85rem !important;
        color: #2d2d3f !important;
    }
    .item-sku-display {
        font-size: 0.75rem;
        color: #6c757d;
        line-height: 1.2;
    }
    .item-sku-display .sku-badge {
        background: #f0ebff;
        color: #6c5ce7;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 0.72rem;
        display: inline-block;
    }
    .item-thumb-edit {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #f0ebff;
    }
    .item-thumb-ph {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f7ff, #eee8ff);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #8E2DE2;
        font-size: 1.2rem;
    }
    .remove-item {
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fee2e2;
        color: #dc2626;
        transition: all 0.2s ease;
        margin: 0 auto;
    }
    .remove-item:hover {
        background: #dc2626;
        color: #fff;
        transform: scale(1.1);
    }
    .calc-input { text-align: right; }

    /* ── Summary ── */
    .q-summary-card { animation-delay: 0.2s; }
    .summary-table { border: none !important; }
    .summary-table td {
        padding: 8px 14px !important;
        border: none !important;
        border-bottom: 1px solid #f5f3ff !important;
        font-size: 0.9rem;
    }
    .summary-table .summary-label {
        color: #666;
        font-weight: 500;
        width: 45%;
    }
    .grand-total-row {
        background: linear-gradient(135deg, #f8f7ff, #eee8ff) !important;
    }
    .grand-total-row td {
        border-bottom: none !important;
        padding: 14px !important;
    }
    .grand-total-label {
        font-weight: 700 !important;
        color: #4A00E0 !important;
        font-size: 1rem !important;
    }
    .grand-total-value input {
        font-weight: 800 !important;
        color: #8E2DE2 !important;
        font-size: 1.1rem !important;
    }

    .notes-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8E2DE2;
        margin-bottom: 8px;
    }
    textarea.form-control {
        border-radius: 10px;
        border: 1.5px solid #e8e5f0;
        resize: vertical;
    }
    textarea.form-control:focus {
        border-color: #8E2DE2;
    }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf

        {{-- ── Header ── --}}
        <div class="q-edit-header">
            <h4><i class="bx bx-plus-circle"></i> Create Quotation</h4>
            <div class="btn-group-actions">
                <a href="{{ route('admin.quotations.index') }}" class="btn btn-cancel-q">
                    <i class="bx bx-x me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-save-q">
                    <i class="bx bx-save me-1"></i> Create Quotation
                </button>
            </div>
        </div>

        {{-- ── Quotation Details ── --}}
        <div class="card q-card mb-4">
            <div class="card-header">
                <strong><i class="bx bx-detail"></i> Quotation Details</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select select2 @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quotation No</label>
                        <input type="text" name="quotation_number" class="form-control" value="{{ old('quotation_number', $quotation_number ?? '') }}" readonly style="background: #f8f7ff;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Items ── --}}
        <div class="card q-card q-items-card mb-4">
            <div class="card-header">
                <strong><i class="bx bx-list-ul"></i> Items</strong>
                <button type="button" class="btn btn-add-item" id="addItemBtn"><i class="bx bx-plus me-1"></i> Add Item</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table items-table mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th style="width: 55px;" class="text-center">Image</th>
                                <th style="width: 25%; min-width: 200px;">Item</th>
                                <th style="width: 9%; min-width: 80px;" class="text-center">Qty</th>
                                <th style="width: 12%; min-width: 100px;" class="text-end">MRP</th>
                                <th style="width: 12%; min-width: 100px;" class="text-end">SDP</th>
                                <th style="width: 10%; min-width: 85px;" class="text-end">Disc (%)</th>
                                <th style="width: 12%; min-width: 100px;" class="text-end">Rate</th>
                                <th style="width: 14%; min-width: 110px;" class="text-end">Total</th>
                                <th style="width: 45px;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow">
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="bx bx-package" style="font-size: 2rem; color: #d4c5f9;"></i>
                                    <p class="mb-0 mt-2">Click <strong>"Add Item"</strong> to add items to this quotation.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Summary & Totals ── --}}
        <div class="card q-card q-summary-card mb-4">
            <div class="card-header">
                <strong><i class="bx bx-calculator"></i> Summary & Totals</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <div class="mb-3">
                            <label class="notes-label"><i class="bx bx-note"></i> Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Add any notes...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="notes-label"><i class="bx bx-file"></i> Terms & Conditions</label>
                            <textarea name="terms_conditions" class="form-control @error('terms_conditions') is-invalid @enderror" rows="3" placeholder="Add terms & conditions...">{{ old('terms_conditions', "1. Valid for 30 days.\n2. Payment terms as agreed.") }}</textarea>
                            @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <input type="hidden" name="discount_type" value="percentage">
                        <input type="hidden" name="discount_value" value="0">
                        <input type="hidden" name="discount_amount" value="0">
                        <div class="table-responsive">
                            <table class="table summary-table">
                            <tr>
                                <td class="summary-label">Subtotal</td>
                                <td><input type="text" name="subtotal" id="subtotal" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('subtotal', '0.00') }}" style="height: 38px; background: #f8f7ff; border: 1.5px solid #e8e5f0; border-radius: 8px;"></td>
                            </tr>
                            <tr>
                                <td class="summary-label align-middle">
                                    <div class="mb-1 fw-semibold">Tax Type</div>
                                    <select name="tax_type" id="tax_type" class="form-select shadow-none w-100" style="border-radius: 8px; border: 1.5px solid #e8e5f0;">
                                        <option value="cgst_sgst" {{ old('tax_type', 'cgst_sgst') == 'cgst_sgst' ? 'selected' : '' }}>CGST + SGST</option>
                                        <option value="igst" {{ old('tax_type') == 'igst' ? 'selected' : '' }}>IGST</option>
                                        <option value="none" {{ old('tax_type') == 'none' ? 'selected' : '' }}>No Tax</option>
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <div class="row g-2 justify-content-end align-items-end" id="taxFields">
                                        <div class="col-6" id="cgst_col">
                                            <label class="form-label text-center d-block small fw-bold mb-1" style="color: #8E2DE2;">CGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="any" name="cgst_percentage" id="cgst_percentage" class="form-control text-center calc-input px-3" value="{{ old('cgst_percentage', 9) }}" style="height: 38px; border-radius: 8px; border: 1.5px solid #e8e5f0;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6" id="sgst_col">
                                            <label class="form-label text-center d-block small fw-bold mb-1" style="color: #8E2DE2;">SGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="any" name="sgst_percentage" id="sgst_percentage" class="form-control text-center calc-input px-3" value="{{ old('sgst_percentage', 9) }}" style="height: 38px; border-radius: 8px; border: 1.5px solid #e8e5f0;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-12" id="igst_col" style="display:none;">
                                            <label class="form-label text-center d-block small fw-bold mb-1" style="color: #8E2DE2;">IGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="any" name="igst_percentage" id="igst_percentage" class="form-control text-center calc-input px-3" value="{{ old('igst_percentage', 18) }}" style="height: 38px; border-radius: 8px; border: 1.5px solid #e8e5f0;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-12" id="no_tax_col" style="display:none;">
                                            <label class="form-label text-center d-block small fw-bold mb-1">&nbsp;</label>
                                            <div class="position-relative">
                                                <input type="number" class="form-control text-center px-3" value="0" style="height: 38px; background-color: #e9ecef; cursor: not-allowed; border-radius: 8px;" readonly>
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="summary-label">Total Tax</td>
                                <td><input type="text" name="total_tax" id="total_tax" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('total_tax', '0.00') }}" style="height: 38px; background: #f8f7ff; border: 1.5px solid #e8e5f0; border-radius: 8px;"></td>
                            </tr>
                            <tr>
                                <td class="summary-label">Round Off</td>
                                <td><input type="text" name="round_off" id="round_off" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('round_off', '0.00') }}" style="height: 38px; background: #f8f7ff; border: 1.5px solid #e8e5f0; border-radius: 8px;"></td>
                            </tr>
                            <tr class="grand-total-row">
                                <td class="grand-total-label">Grand Total</td>
                                <td class="grand-total-value"><input type="text" name="grand_total" id="grand_total" class="form-control calc-input fw-bold text-end shadow-none px-3" readonly value="{{ old('grand_total', '0.00') }}" style="height: 42px; background: #fff; border: 2px solid #8E2DE2; border-radius: 8px;"></td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<template id="itemRowTemplate">
    <tr class="item-row">
        <td class="item-sr text-center fw-bold text-muted"></td>
        <td class="text-center">
            <img src="" class="item-image-preview item-thumb-edit d-none">
            <div class="item-thumb-ph item-thumb-placeholder">
                <i class="bx bx-package"></i>
            </div>
        </td>
        <td>
            <select class="form-select form-select-sm item-select" name="items[__INDEX__][item_id]" required>
                <option value="">Search item...</option>
            </select>
            <input type="hidden" name="items[__INDEX__][item_name]" class="item-name-input" value="">
            <input type="hidden" name="items[__INDEX__][sku]" class="sku-input" value="">
            <div class="item-sku-display mt-1" style="display: none;">
                <span class="sku-badge">SKU: <span class="sku-text"></span></span>
            </div>
        </td>
        <td><input type="number" step="any" name="items[__INDEX__][quantity]" class="form-control form-control-sm quantity-input text-center" value="1" min="0.01" required style="border: 1px solid #e8e5f0;"></td>
        <td><input type="number" step="any" name="items[__INDEX__][mrp]" class="form-control form-control-sm mrp-input calc-input text-end" value="0" min="0" style="border: 1px solid #e8e5f0;"></td>
        <td><input type="number" step="any" name="items[__INDEX__][sdp]" class="form-control form-control-sm sdp-input calc-input text-end" value="0" min="0" style="border: 1px solid #e8e5f0;"></td>
        <td><input type="number" step="any" name="items[__INDEX__][discount_percentage]" class="form-control form-control-sm disc-pct-input calc-input text-end" value="0" min="0" max="100" style="border: 1px solid #e8e5f0;"></td>
        <td>
            <input type="number" step="any" name="items[__INDEX__][rate]" class="form-control form-control-sm rate-input calc-input text-end" value="0" min="0" required style="border: 1px solid #e8e5f0;">
            <input type="hidden" name="items[__INDEX__][discount_amount]" class="disc-amt-input" value="0">
        </td>
        <td><input type="text" name="items[__INDEX__][total]" class="form-control form-control-sm total-input calc-input text-end fw-bold" readonly value="0.00" style="background: #f8f7ff; border: 1px solid #e8e5f0; color: #8E2DE2;"></td>
        <td class="text-center"><div class="remove-item"><i class="bx bx-trash fs-6"></i></div></td>
    </tr>
</template>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var itemIndex = 0;

function cleanItemName(text) {
    if (!text) return '';
    return text.replace(/\s*\([^\)]*\)$/, '').trim();
}

function formatItem(item) {
    if (!item.id) { return item.text; }
    var image = item.image || null;
    var rate = item.rate || 0;
    var mrp = item.mrp || rate;
    var sdp = item.sdp || 0;
    var sku = item.sku || '';
    var displayName = item.name || cleanItemName(item.text);
    
    var imgHtml = image ? '<img src="' + image + '" class="rounded me-2" style="width:32px; height:32px; object-fit:cover;">' : '<div class="rounded me-2 bg-light border d-inline-block" style="width:32px; height:32px;"></div>';
    
    return $(
        '<div class="d-flex align-items-center py-1">' +
            imgHtml +
            '<div style="overflow: hidden;">' +
                '<div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem; line-height: 1.2;">' + displayName + '</div>' +
                '<div class="text-muted" style="font-size: 0.75rem;">' + (sku ? 'SKU: ' + sku + ' | ' : '') + 'MRP: ₹' + formatNum(mrp) + ' | SDP: ₹' + formatNum(sdp) + '</div>' +
            '</div>' +
        '</div>'
    );
}

function formatCustomer(customer) {
    if (!customer.id) { return customer.text; }
    var textParts = customer.text.split(' (');
    var name = textParts[0];
    var email = textParts[1] ? textParts[1].replace(')', '') : '';
    
    return $(
        '<div class="py-1">' +
            '<div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.2;">' + name + '</div>' +
            (email ? '<div class="text-muted" style="font-size: 0.75rem;"><i class="bx bx-envelope"></i> ' + email + '</div>' : '') +
        '</div>'
    );
}

function formatNum(val) {
    var num = parseFloat(val) || 0;
    if (Number.isInteger(num) || Math.abs(num - Math.round(num)) < 0.00001) {
        return Math.round(num).toString();
    }
    return num.toFixed(2);
}

function calculateRow(row, sourceField) {
    var qty = parseFloat(row.find('.quantity-input').val()) || 0;
    var mrp = parseFloat(row.find('.mrp-input').val()) || 0;
    var discPct = parseFloat(row.find('.disc-pct-input').val()) || 0;
    var rate = parseFloat(row.find('.rate-input').val()) || 0;

    if (sourceField === 'disc_pct' || sourceField === 'mrp') {
        var discAmtPerUnit = (mrp * discPct) / 100;
        rate = mrp - discAmtPerUnit;
        if (rate < 0) rate = 0;
        row.find('.rate-input').val(formatNum(rate));
    } else if (sourceField === 'rate') {
        if (mrp > 0) {
            discPct = ((mrp - rate) / mrp) * 100;
            if (discPct < 0) discPct = 0;
            row.find('.disc-pct-input').val(formatNum(discPct));
        }
    } else {
        if (discPct > 0 && mrp > 0) {
            rate = mrp - ((mrp * discPct) / 100);
            row.find('.rate-input').val(formatNum(rate));
        } else if (mrp > 0 && rate === 0) {
            rate = mrp;
            row.find('.rate-input').val(formatNum(rate));
        }
    }

    var discAmountTotal = (mrp - rate) * qty;
    if (discAmountTotal < 0) discAmountTotal = 0;
    row.find('.disc-amt-input').val(formatNum(discAmountTotal));

    var total = qty * rate;
    row.find('.total-input').val(formatNum(total));
    calculateSummary();
}

function calculateSummary() {
    var subtotal = 0;
    $('.item-row').each(function(){
        var total = parseFloat($(this).find('.total-input').val()) || 0;
        subtotal += total;
    });
    $('#subtotal').val(formatNum(subtotal));

    var afterDiscount = subtotal;
    
    var taxType = $('select[name="tax_type"]').val();
    var totalTax = 0;
    
    if (taxType === 'igst') {
        var igstPct = parseFloat($('#igst_percentage').val()) || 0;
        totalTax = (igstPct / 100) * afterDiscount;
    } else if (taxType === 'cgst_sgst') {
        var cgstPct = parseFloat($('#cgst_percentage').val()) || 0;
        var sgstPct = parseFloat($('#sgst_percentage').val()) || 0;
        totalTax = ((cgstPct + sgstPct) / 100) * afterDiscount;
    }

    $('#total_tax').val(formatNum(totalTax));

    var grandTotal = afterDiscount + totalTax;
    var roundOff = 0;

    $('#round_off').val(formatNum(roundOff));
    $('#grand_total').val(formatNum(grandTotal));
}

function renumberRows() {
    $('.item-row').each(function(idx){
        $(this).find('.item-sr').text(idx + 1);
    });
    if ($('.item-row').length === 0) {
        $('#noItemsRow').show();
    } else {
        $('#noItemsRow').hide();
    }
}

function addItemRow(data) {
    var template = $('#itemRowTemplate').html();
    var row = $(template.replace(/__INDEX__/g, itemIndex));
    itemIndex++;

    $('#noItemsRow').hide();
    $('#itemsBody').append(row);
    renumberRows();

    var $select = row.find('.item-select');

    if (data && (data.item_id || data.id || data.item_name)) {
        var itemId = data.item_id || '';
        var itemName = data.item_name || (data.item ? data.item.name : (data.name || cleanItemName(data.text)));
        var sku = data.sku || (data.item ? data.item.sku : '');
        var mrp = data.mrp !== undefined && data.mrp !== null ? data.mrp : (data.item && data.item.mrp ? data.item.mrp : (data.rate || 0));
        var sdp = data.sdp !== undefined && data.sdp !== null ? data.sdp : (data.item && data.item.sdp ? data.item.sdp : 0);
        var discPct = data.discount_percentage !== undefined && data.discount_percentage !== null ? data.discount_percentage : 0;
        var rate = data.rate !== undefined && data.rate !== null ? data.rate : 0;
        var quantity = data.quantity !== undefined && data.quantity !== null ? data.quantity : 1;
        var total = data.total !== undefined && data.total !== null ? data.total : (quantity * rate);

        if (itemId || itemName) {
            var option = new Option(itemName, itemId, true, true);
            $(option).data('itemData', data);
            $select.append(option);
        }

        var imageUrl = data.image || (data.item ? (data.item.image || null) : null);
        if (imageUrl) {
            row.find('.item-image-preview').attr('src', imageUrl).removeClass('d-none');
            row.find('.item-thumb-placeholder').addClass('d-none');
        } else {
            row.find('.item-image-preview').addClass('d-none').attr('src', '');
            row.find('.item-thumb-placeholder').removeClass('d-none');
        }

        row.find('.item-name-input').val(itemName);
        row.find('.sku-input').val(sku);
        if (sku) {
            row.find('.sku-text').text(sku);
            row.find('.item-sku-display').show();
        } else {
            row.find('.item-sku-display').hide();
        }
        row.find('.quantity-input').val(formatNum(quantity));
        row.find('.mrp-input').val(formatNum(mrp));
        row.find('.sdp-input').val(formatNum(sdp));
        row.find('.disc-pct-input').val(formatNum(discPct));
        row.find('.rate-input').val(formatNum(rate));
        row.find('.disc-amt-input').val(formatNum(data.discount_amount || 0));
        row.find('.total-input').val(formatNum(total));
    }

    $select.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search item...',
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("admin.items.search.ajax") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                return {
                    results: data.results,
                    pagination: data.pagination
                };
            },
            cache: true
        },
        templateResult: formatItem,
        templateSelection: function(item) {
            if (!item.id && !item.text) return 'Search item...';
            return item.name || cleanItemName(item.text) || 'Search item...';
        }
    }).on('select2:select', function(e){
        var itemData = e.params.data;
        var rate = itemData.rate || 0;
        var mrp = itemData.mrp !== undefined && itemData.mrp !== null ? itemData.mrp : rate;
        var sdp = itemData.sdp !== undefined && itemData.sdp !== null ? itemData.sdp : 0;
        var sku = itemData.sku || '';
        var itemName = itemData.name || cleanItemName(itemData.text);
        var imageUrl = itemData.image || null;

        if (imageUrl) {
            row.find('.item-image-preview').attr('src', imageUrl).removeClass('d-none');
            row.find('.item-thumb-placeholder').addClass('d-none');
        } else {
            row.find('.item-image-preview').addClass('d-none').attr('src', '');
            row.find('.item-thumb-placeholder').removeClass('d-none');
        }

        row.find('.item-name-input').val(itemName);
        row.find('.sku-input').val(sku);
        if (sku) {
            row.find('.sku-text').text(sku);
            row.find('.item-sku-display').show();
        } else {
            row.find('.item-sku-display').hide();
        }
        row.find('.mrp-input').val(formatNum(mrp));
        row.find('.sdp-input').val(formatNum(sdp));
        row.find('.disc-pct-input').val(0);
        row.find('.rate-input').val(formatNum(mrp));
        calculateRow(row);
    });

    row.find('.quantity-input').on('input', function(){
        calculateRow(row, 'qty');
    });
    row.find('.mrp-input').on('input', function(){
        calculateRow(row, 'mrp');
    });
    row.find('.sdp-input').on('input', function(){
        calculateRow(row, 'sdp');
    });
    row.find('.disc-pct-input').on('input', function(){
        calculateRow(row, 'disc_pct');
    });
    row.find('.rate-input').on('input', function(){
        calculateRow(row, 'rate');
    });

    row.find('.remove-item').on('click', function(){
        row.find('.item-select').select2('destroy');
        row.remove();
        renumberRows();
        calculateSummary();
    });

    if (data) {
        calculateRow(row);
    }
}

$(document).ready(function(){
    $('.select2').not('.item-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        templateResult: function(item) {
            if ($(item.element).closest('select').attr('id') === 'customer_id') {
                return formatCustomer(item);
            }
            return item.text;
        },
        templateSelection: function(item) {
            if ($(item.element).closest('select').attr('id') === 'customer_id' && item.id) {
                return item.text.split(' (')[0];
            }
            return item.text;
        }
    });

    $('#addItemBtn').on('click', function(){
        addItemRow();
    });

    $('select[name="tax_type"]').on('change', function(){
        var val = $(this).val();
        if (val === 'none') {
            $('#taxFields').show();
            $('#cgst_col, #sgst_col, #igst_col').hide();
            $('#no_tax_col').show();
            $('#taxFields input').val(0).prop('readonly', true);
        } else {
            $('#taxFields').show();
            $('#no_tax_col').hide();
            $('#taxFields input').prop('readonly', false);
            if (val === 'igst') {
                $('#cgst_col, #sgst_col').hide();
                $('#igst_col').show();
                $('#cgst_percentage').val(0).prop('readonly', true);
                $('#sgst_percentage').val(0).prop('readonly', true);
                if ($('#igst_percentage').val() == 0) $('#igst_percentage').val(18);
            } else {
                $('#igst_col').hide();
                $('#cgst_col, #sgst_col').show();
                $('#igst_percentage').val(0).prop('readonly', true);
                if ($('#cgst_percentage').val() == 0) $('#cgst_percentage').val(9);
                if ($('#sgst_percentage').val() == 0) $('#sgst_percentage').val(9);
            }
        }
        calculateSummary();
    });

    $('#cgst_percentage, #sgst_percentage, #igst_percentage').on('input', function(){
        calculateSummary();
    });

    var savedTaxType = '{{ old('tax_type', 'cgst_sgst') }}';
    $('select[name="tax_type"]').val(savedTaxType).trigger('change');

    function updateValidUntil() {
        var dateVal = $('input[name="date"]').val();
        if (dateVal) {
            var dateObj = new Date(dateVal);
            dateObj.setDate(dateObj.getDate() + 30);
            var year = dateObj.getFullYear();
            var month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
            var day = ('0' + dateObj.getDate()).slice(-2);
            var validUntilVal = year + '-' + month + '-' + day;
            $('input[name="valid_until"]').val(validUntilVal);
        }
    }

    $('input[name="date"]').on('change', function() {
        updateValidUntil();
    });

    if (!$('input[name="valid_until"]').val()) {
        updateValidUntil();
    }

    @if(old('items'))
        var oldItems = @json(old('items'));
        $.each(oldItems, function(idx, item){
            addItemRow(item);
        });
    @else
        addItemRow();
    @endif
});
</script>
@endsection
