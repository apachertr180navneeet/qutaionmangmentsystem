@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.item-row td { vertical-align: middle; }
.item-row .form-control, .item-row .form-select { font-size: 0.85rem; }
.remove-item { cursor: pointer; }
.calc-input { text-align: right; }
.summary-table td { padding: 6px 12px; }
.summary-table td:last-child { text-align: right; font-weight: 600; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Create Quotation</span>
    </h5>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf
        <div class="card mb-3">
            <div class="card-header"><strong>Quotation Details</strong></div>
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
                    <div class="col-md-3">
                        <label class="form-label">Quotation Number</label>
                        <input type="text" name="quotation_number" class="form-control" value="{{ old('quotation_number', $quotation_number ?? '') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Items</strong>
                <button type="button" class="btn btn-primary btn-sm" id="addItemBtn"><i class="bx bx-plus"></i> Add Item</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th style="min-width:180px;">Item</th>
                                <th style="width:70px;">HSN</th>
                                <th style="width:60px;">Unit</th>
                                <th style="width:70px;">Qty</th>
                                <th style="width:90px;">Rate</th>
                                <th style="width:70px;">Disc%</th>
                                <th style="width:90px;">Disc Amt</th>
                                <th style="width:100px;">Taxable Value</th>
                                <th style="width:70px;">CGST%</th>
                                <th style="width:85px;">CGST Amt</th>
                                <th style="width:70px;">SGST%</th>
                                <th style="width:85px;">SGST Amt</th>
                                <th style="width:100px;">Total</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow">
                                <td colspan="15" class="text-center text-muted py-3">Click "Add Item" to add items to this quotation.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Summary & Totals</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="terms_conditions" class="form-control @error('terms_conditions') is-invalid @enderror" rows="3">{{ old('terms_conditions') }}</textarea>
                            @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered summary-table">
                            <tr>
                                <td>Subtotal</td>
                                <td><input type="text" name="subtotal" id="subtotal" class="form-control form-control-sm calc-input" readonly value="0.00"></td>
                            </tr>
                            <tr>
                                <td>
                                    Discount Type<br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="discount_type" id="discount_type_percentage" value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_type_percentage">%</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="discount_type" id="discount_type_fixed" value="fixed" {{ old('discount_type') == 'fixed' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_type_fixed">Fixed</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="row g-1">
                                        <div class="col-6">
                                            <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control form-control-sm calc-input" value="{{ old('discount_value', 0) }}" placeholder="Value">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="discount_amount" id="discount_amount" class="form-control form-control-sm calc-input" readonly value="0.00" placeholder="Amount">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tax Type<br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tax_type" id="tax_type_cgst_sgst" value="cgst_sgst" {{ old('tax_type', 'cgst_sgst') == 'cgst_sgst' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tax_type_cgst_sgst">CGST+SGST</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tax_type" id="tax_type_igst" value="igst" {{ old('tax_type') == 'igst' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tax_type_igst">IGST</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tax_type" id="tax_type_none" value="none" {{ old('tax_type') == 'none' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tax_type_none">No Tax</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="row g-1" id="taxFields">
                                        <div class="col-4">
                                            <label class="form-label small">CGST%</label>
                                            <input type="number" step="0.01" name="cgst_percentage" id="cgst_percentage" class="form-control form-control-sm calc-input" value="{{ old('cgst_percentage', 9) }}">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small">SGST%</label>
                                            <input type="number" step="0.01" name="sgst_percentage" id="sgst_percentage" class="form-control form-control-sm calc-input" value="{{ old('sgst_percentage', 9) }}">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small">IGST%</label>
                                            <input type="number" step="0.01" name="igst_percentage" id="igst_percentage" class="form-control form-control-sm calc-input" value="{{ old('igst_percentage', 18) }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Tax</td>
                                <td><input type="text" name="total_tax" id="total_tax" class="form-control form-control-sm calc-input" readonly value="0.00"></td>
                            </tr>
                            <tr>
                                <td>Round Off</td>
                                <td><input type="text" name="round_off" id="round_off" class="form-control form-control-sm calc-input" readonly value="0.00"></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Grand Total</strong></td>
                                <td><input type="text" name="grand_total" id="grand_total" class="form-control form-control-sm calc-input fw-bold" readonly value="0.00"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3 pb-4 text-center">
            <button type="submit" name="submit_action" value="draft" class="btn btn-secondary"><i class="bx bx-save"></i> Save as Draft</button>
            <button type="submit" name="submit_action" value="sent" class="btn btn-primary"><i class="bx bx-send"></i> Send</button>
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<template id="itemRowTemplate">
    <tr class="item-row">
        <td class="item-sr text-center"></td>
        <td>
            <select class="form-select form-select-sm item-select" name="items[__INDEX__][item_id]" required>
                <option value="">Search item...</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-rate="{{ $item->rate }}" data-tax="{{ $item->tax_percentage }}" data-unit="{{ $item->unit }}" data-hsn="{{ $item->hsn_code }}" data-name="{{ $item->name }}">{{ $item->name }} ({{ $item->sku }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="items[__INDEX__][hsn_code]" class="form-control form-control-sm hsn-input" readonly></td>
        <td><input type="text" name="items[__INDEX__][unit]" class="form-control form-control-sm unit-input" readonly></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][quantity]" class="form-control form-control-sm quantity-input calc-input" value="1" min="0.01"></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][rate]" class="form-control form-control-sm rate-input calc-input" value="0" min="0"></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][discount_percentage]" class="form-control form-control-sm discount-pct-input calc-input" value="0" min="0" max="100"></td>
        <td><input type="text" name="items[__INDEX__][discount_amount]" class="form-control form-control-sm discount-amt-input calc-input" readonly value="0.00"></td>
        <td><input type="text" name="items[__INDEX__][taxable_value]" class="form-control form-control-sm taxable-input calc-input" readonly value="0.00"></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][cgst_percentage]" class="form-control form-control-sm cgst-pct-input calc-input" value="9" min="0"></td>
        <td><input type="text" name="items[__INDEX__][cgst_amount]" class="form-control form-control-sm cgst-amt-input calc-input" readonly value="0.00"></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][sgst_percentage]" class="form-control form-control-sm sgst-pct-input calc-input" value="9" min="0"></td>
        <td><input type="text" name="items[__INDEX__][sgst_amount]" class="form-control form-control-sm sgst-amt-input calc-input" readonly value="0.00"></td>
        <td><input type="text" name="items[__INDEX__][total]" class="form-control form-control-sm total-input calc-input" readonly value="0.00"></td>
        <td class="text-center"><i class="bx bx-trash text-danger remove-item fs-5"></i></td>
    </tr>
</template>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var itemIndex = 0;

function calculateRow(row) {
    var qty = parseFloat(row.find('.quantity-input').val()) || 0;
    var rate = parseFloat(row.find('.rate-input').val()) || 0;
    var discPct = parseFloat(row.find('.discount-pct-input').val()) || 0;
    var cgstPct = parseFloat(row.find('.cgst-pct-input').val()) || 0;
    var sgstPct = parseFloat(row.find('.sgst-pct-input').val()) || 0;

    var amount = qty * rate;
    var discAmt = (discPct / 100) * amount;
    row.find('.discount-amt-input').val(discAmt.toFixed(2));
    var taxable = amount - discAmt;
    row.find('.taxable-input').val(taxable.toFixed(2));

    var cgstAmt = (cgstPct / 100) * taxable;
    var sgstAmt = (sgstPct / 100) * taxable;
    row.find('.cgst-amt-input').val(cgstAmt.toFixed(2));
    row.find('.sgst-amt-input').val(sgstAmt.toFixed(2));

    var total = taxable + cgstAmt + sgstAmt;
    row.find('.total-input').val(total.toFixed(2));

    calculateSummary();
}

function calculateSummary() {
    var subtotal = 0;
    var totalTax = 0;

    $('.item-row').each(function(){
        var total = parseFloat($(this).find('.total-input').val()) || 0;
        subtotal += total;
        var cgst = parseFloat($(this).find('.cgst-amt-input').val()) || 0;
        var sgst = parseFloat($(this).find('.sgst-amt-input').val()) || 0;
        totalTax += cgst + sgst;
    });

    $('#subtotal').val(subtotal.toFixed(2));
    $('#total_tax').val(totalTax.toFixed(2));

    var discType = $('input[name="discount_type"]:checked').val();
    var discVal = parseFloat($('#discount_value').val()) || 0;
    var discAmt = 0;
    if (discType === 'percentage') {
        discAmt = (discVal / 100) * subtotal;
    } else {
        discAmt = discVal;
    }
    $('#discount_amount').val(discAmt.toFixed(2));

    var afterDiscount = subtotal - discAmt;
    var grandTotal = afterDiscount + totalTax;
    var roundOff = Math.round(grandTotal) - grandTotal;
    grandTotal = Math.round(grandTotal);

    $('#round_off').val(roundOff.toFixed(2));
    $('#grand_total').val(grandTotal.toFixed(2));
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

    if (data) {
        row.find('.item-select').val(data.item_id);
        row.find('.hsn-input').val(data.hsn || '');
        row.find('.unit-input').val(data.unit || '');
        row.find('.quantity-input').val(data.quantity || 1);
        row.find('.rate-input').val(data.rate || 0);
        row.find('.discount-pct-input').val(data.discount_percentage || 0);
        row.find('.cgst-pct-input').val(data.cgst_percentage || 9);
        row.find('.sgst-pct-input').val(data.sgst_percentage || 9);
    }

    $('#noItemsRow').hide();
    $('#itemsBody').append(row);
    renumberRows();

    row.find('.item-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search item...',
        dropdownParent: row
    }).on('change', function(){
        var selected = $(this).find(':selected');
        var rate = selected.data('rate') || 0;
        var tax = selected.data('tax') || 0;
        var unit = selected.data('unit') || '';
        var hsn = selected.data('hsn') || '';
        var cgst = tax / 2;
        var sgst = tax / 2;
        row.find('.rate-input').val(rate);
        row.find('.unit-input').val(unit);
        row.find('.hsn-input').val(hsn);
        row.find('.cgst-pct-input').val(cgst.toFixed(2));
        row.find('.sgst-pct-input').val(sgst.toFixed(2));
        calculateRow(row);
    });

    row.find('.quantity-input, .rate-input, .discount-pct-input, .cgst-pct-input, .sgst-pct-input').on('input', function(){
        calculateRow(row);
    });

    row.find('.remove-item').on('click', function(){
        row.find('.item-select').select2('destroy');
        row.remove();
        renumberRows();
        calculateSummary();
    });

    if (data) {
        calculateRow(row);
    } else {
        row.find('.item-select').trigger('change');
    }
}

$(document).ready(function(){
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#addItemBtn').on('click', function(){
        addItemRow();
    });

    $('input[name="discount_type"]').on('change', function(){
        calculateSummary();
    });

    $('#discount_value').on('input', function(){
        calculateSummary();
    });

    $('input[name="tax_type"]').on('change', function(){
        var val = $(this).val();
        if (val === 'none') {
            $('#taxFields input').val(0).prop('readonly', true);
        } else {
            $('#taxFields input').prop('readonly', false);
            if (val === 'igst') {
                $('#cgst_percentage').val(0).prop('readonly', true);
                $('#sgst_percentage').val(0).prop('readonly', true);
                if ($('#igst_percentage').val() == 0) $('#igst_percentage').val(18);
            } else {
                $('#igst_percentage').val(0).prop('readonly', true);
                if ($('#cgst_percentage').val() == 0) $('#cgst_percentage').val(9);
                if ($('#sgst_percentage').val() == 0) $('#sgst_percentage').val(9);
            }
        }
    });

    $('#cgst_percentage, #sgst_percentage, #igst_percentage').on('input', function(){
        var taxType = $('input[name="tax_type"]:checked').val();
        $('.item-row').each(function(){
            var cgstPct, sgstPct;
            if (taxType === 'igst') {
                var igst = parseFloat($('#igst_percentage').val()) || 0;
                cgstPct = igst / 2;
                sgstPct = igst / 2;
            } else if (taxType === 'none') {
                cgstPct = 0;
                sgstPct = 0;
            } else {
                cgstPct = parseFloat($('#cgst_percentage').val()) || 0;
                sgstPct = parseFloat($('#sgst_percentage').val()) || 0;
            }
            $(this).find('.cgst-pct-input').val(cgstPct.toFixed(2));
            $(this).find('.sgst-pct-input').val(sgstPct.toFixed(2));
            calculateRow($(this));
        });
    });

    $('input[name="tax_type"]:checked').trigger('change');

    @if(old('items'))
        var oldItems = @json(old('items', []));
        $.each(oldItems, function(idx, item){
            addItemRow(item);
        });
    @endif
});
</script>
@endsection
