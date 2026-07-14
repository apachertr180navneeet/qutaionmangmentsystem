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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0" style="background: -webkit-linear-gradient(45deg, #8E2DE2, #4A00E0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Create Quotation
        </h4>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm" class="form-animated">
        @csrf
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title text-secondary fw-bold mb-4">Quotation Details</h5>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">CUSTOMER <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select select2 @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">QUOTATION NUMBER</label>
                        <input type="text" name="quotation_number" class="form-control" value="{{ old('quotation_number', $quotation_number ?? '') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">DATE <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">VALID UNTIL</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title text-secondary fw-bold mb-0">Items</h5>
                    <button type="button" class="btn btn-success shadow-sm rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; font-weight: 500;" id="addItemBtn">
                        <i class="bx bx-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-borderless table-hover mb-0" id="itemsTable">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 50px;">#</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 60px;">IMAGE</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 200px;">ITEM</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 100px;">HSN</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 100px;">UNIT</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 100px;">QTY</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 120px;">RATE</th>
                                <th class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 120px;">TOTAL</th>
                                <th style="min-width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="border-bottom">
                            <tr id="noItemsRow">
                                <td colspan="9" class="text-center text-muted py-4">Click "Add Item" to add items to this quotation.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title text-secondary fw-bold mb-4">Summary & Totals</h5>
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">NOTES</label>
                            <textarea name="notes" class="form-control rounded-3 shadow-none border @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">TERMS & CONDITIONS</label>
                            <textarea name="terms_conditions" class="form-control rounded-3 shadow-none border @error('terms_conditions') is-invalid @enderror" rows="4">{{ old('terms_conditions') }}</textarea>
                            @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="table-responsive p-3 bg-white rounded-3 border shadow-sm">
                            <table class="table table-borderless summary-table mb-0">
                            <tr class="border-bottom">
                                <td class="text-muted py-3">Subtotal</td>
                                <td class="py-3"><input type="text" name="subtotal" id="subtotal" class="form-control form-control-sm calc-input border-0 bg-transparent text-end p-0 m-0" readonly value="0.00"></td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-3 align-middle">
                                    <div class="mb-1 text-dark" style="font-size: 0.85rem;">Discount Type</div>
                                    <div class="d-flex gap-2">
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input mt-1" type="radio" name="discount_type" id="discount_type_percentage" value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" style="font-size: 0.85rem;" for="discount_type_percentage">%</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input mt-1" type="radio" name="discount_type" id="discount_type_fixed" value="fixed" {{ old('discount_type') == 'fixed' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" style="font-size: 0.85rem;" for="discount_type_fixed">Fixed</label>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 align-middle">
                                    <div class="row g-2 justify-content-end mt-1">
                                        <div class="col-5">
                                            <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control form-control-sm text-center shadow-none" value="{{ old('discount_value', 0) }}" placeholder="Value">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" name="discount_amount" id="discount_amount" class="form-control form-control-sm calc-input bg-transparent border-0 text-end shadow-none p-0" readonly value="0.00" placeholder="Amount">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-3 align-middle">
                                    <div class="mb-1 text-dark" style="font-size: 0.85rem;">Tax Type</div>
                                    <div class="d-flex gap-2">
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input mt-1" type="radio" name="tax_type" id="tax_type_cgst_sgst" value="cgst_sgst" {{ old('tax_type', 'cgst_sgst') == 'cgst_sgst' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" style="font-size: 0.85rem;" for="tax_type_cgst_sgst">CGST+SGST</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input mt-1" type="radio" name="tax_type" id="tax_type_igst" value="igst" {{ old('tax_type') == 'igst' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" style="font-size: 0.85rem;" for="tax_type_igst">IGST</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input mt-1" type="radio" name="tax_type" id="tax_type_none" value="none" {{ old('tax_type') == 'none' ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark" style="font-size: 0.85rem;" for="tax_type_none">No Tax</label>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 align-middle">
                                    <div class="row g-2 justify-content-end" id="taxFields">
                                        <div class="col-4">
                                            <label class="form-label text-center d-block text-muted mb-1" style="font-size: 0.65rem;">CGST%</label>
                                            <input type="number" step="0.01" name="cgst_percentage" id="cgst_percentage" class="form-control form-control-sm text-center shadow-none p-1" value="{{ old('cgst_percentage', 9) }}">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-center d-block text-muted mb-1" style="font-size: 0.65rem;">SGST%</label>
                                            <input type="number" step="0.01" name="sgst_percentage" id="sgst_percentage" class="form-control form-control-sm text-center shadow-none p-1" value="{{ old('sgst_percentage', 9) }}">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-center d-block text-muted mb-1" style="font-size: 0.65rem;">IGST%</label>
                                            <input type="number" step="0.01" name="igst_percentage" id="igst_percentage" class="form-control form-control-sm text-center shadow-none p-1" value="{{ old('igst_percentage', 18) }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-3">Total Tax</td>
                                <td class="py-3"><input type="text" name="total_tax" id="total_tax" class="form-control form-control-sm calc-input border-0 bg-transparent text-end p-0 m-0" readonly value="0.00"></td>
                            </tr>
                            <tr>
                                <td class="text-muted py-3">Round Off</td>
                                <td class="py-3"><input type="text" name="round_off" id="round_off" class="form-control form-control-sm calc-input border-0 bg-transparent text-end p-0 m-0" readonly value="0.00"></td>
                            </tr>
                            <tr class="bg-light rounded">
                                <td class="py-3"><strong class="text-secondary ps-2">Grand Total</strong></td>
                                <td class="py-3"><input type="text" name="grand_total" id="grand_total" class="form-control form-control-sm calc-input fw-bold bg-transparent border-0 text-end pe-2 m-0 shadow-none" readonly value="0.00" style="font-size: 1.1rem;"></td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 pt-2 pb-5 text-center">
            <button type="submit" name="submit_action" value="draft" class="btn btn-secondary shadow-sm rounded-pill px-4">
                <i class="bx bx-save me-1"></i> Save as Draft
            </button>
            <button type="submit" name="submit_action" value="sent" class="btn btn-primary shadow-sm rounded-pill px-5" style="background: linear-gradient(135deg, #8E2DE2, #4A00E0); border: none;">
                <i class="bx bx-send me-1"></i> Send
            </button>
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 bg-white">Cancel</a>
        </div>
    </form>
</div>

<template id="itemRowTemplate">
    <tr class="item-row">
        <td class="item-sr text-center"></td>
        <td class="text-center">
            <img src="" class="item-image-preview img-thumbnail d-none" style="width:40px; height:40px; object-fit:cover;">
        </td>
        <td>
            <select class="form-select form-select-sm item-select" name="items[__INDEX__][item_id]" required>
                <option value="">Search item...</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-rate="{{ $item->rate }}" data-tax="{{ $item->tax_percentage }}" data-unit="{{ $item->unit }}" data-hsn="{{ $item->hsn_code }}" data-name="{{ $item->name }}" data-image="{{ $item->image }}">{{ $item->name }} ({{ $item->sku }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="items[__INDEX__][hsn_code]" class="form-control form-control-sm hsn-input" readonly></td>
        <td><input type="text" name="items[__INDEX__][unit]" class="form-control form-control-sm unit-input" readonly></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][quantity]" class="form-control form-control-sm quantity-input calc-input" value="1" min="0.01"></td>
        <td><input type="number" step="0.01" name="items[__INDEX__][rate]" class="form-control form-control-sm rate-input calc-input" value="0" min="0"></td>
        <td>
            <input type="hidden" name="items[__INDEX__][discount_percentage]" class="discount-pct-input calc-input" value="0">
            <input type="hidden" name="items[__INDEX__][discount_amount]" class="discount-amt-input calc-input" value="0.00">
            <input type="hidden" name="items[__INDEX__][taxable_value]" class="taxable-input calc-input" value="0.00">
            <input type="hidden" name="items[__INDEX__][cgst_percentage]" class="cgst-pct-input calc-input" value="9">
            <input type="hidden" name="items[__INDEX__][cgst_amount]" class="cgst-amt-input calc-input" value="0.00">
            <input type="hidden" name="items[__INDEX__][sgst_percentage]" class="sgst-pct-input calc-input" value="9">
            <input type="hidden" name="items[__INDEX__][sgst_amount]" class="sgst-amt-input calc-input" value="0.00">
            <input type="text" name="items[__INDEX__][total]" class="form-control form-control-sm total-input calc-input" readonly value="0.00">
        </td>
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
        
        var selectedOption = row.find('.item-select option[value="'+data.item_id+'"]');
        var imageUrl = selectedOption.data('image');
        if (imageUrl) {
            row.find('.item-image-preview').attr('src', imageUrl).removeClass('d-none');
        }

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
        placeholder: 'Search item...'
    }).on('change', function(){
        var selected = $(this).find(':selected');
        var rate = selected.data('rate') || 0;
        var tax = selected.data('tax') || 0;
        var unit = selected.data('unit') || '';
        var hsn = selected.data('hsn') || '';
        var imageUrl = selected.data('image');

        if (imageUrl) {
            row.find('.item-image-preview').attr('src', imageUrl).removeClass('d-none');
        } else {
            row.find('.item-image-preview').addClass('d-none').attr('src', '');
        }

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
        var oldItems = @json(old('items', []));
        $.each(oldItems, function(idx, item){
            addItemRow(item);
        });
    @endif
});
</script>
@endsection
