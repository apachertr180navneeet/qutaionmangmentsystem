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
        <span class="text-primary fw-light">Edit Quotation</span>
    </h5>

    <form action="{{ route('admin.quotations.update', $quotation->id) }}" method="POST" id="quotationForm">
        @csrf @method('PUT')
        <div class="card mb-3">
            <div class="card-header"><strong>Quotation Details</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select select2 @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quotation Number</label>
                        <input type="text" name="quotation_number" class="form-control" value="{{ old('quotation_number', $quotation->quotation_number) }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $quotation->created_at ? date('Y-m-d', strtotime($quotation->created_at)) : date('Y-m-d')) }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until', $quotation->valid_until) }}">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                <th style="min-width: 50px;">#</th>
                                <th style="min-width: 60px;">Image</th>
                                <th style="min-width: 200px;">Item</th>
                                <th style="min-width: 100px;">HSN</th>
                                <th style="min-width: 100px;">Unit</th>
                                <th style="min-width: 100px;">Qty</th>
                                <th style="min-width: 120px;">Rate</th>
                                <th style="min-width: 120px;">Total</th>
                                <th style="min-width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow">
                                <td colspan="9" class="text-center text-muted py-3">Click "Add Item" to add items to this quotation.</td>
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
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $quotation->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="terms_conditions" class="form-control @error('terms_conditions') is-invalid @enderror" rows="3">{{ old('terms_conditions', $quotation->terms_conditions) }}</textarea>
                            @error('terms_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered summary-table">
                            <tr>
                                <td class="text-muted" style="width: 45%;">Subtotal</td>
                                <td><input type="text" name="subtotal" id="subtotal" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('subtotal', $quotation->subtotal ?? '0.00') }}" style="height: 38px;"></td>
                            </tr>
                            <tr>
                                <td class="align-middle" style="width: 45%;">
                                    <div class="mb-1">Discount Type</div>
                                    <select name="discount_type" id="discount_type" class="form-select shadow-none w-100">
                                        <option value="percentage" {{ old('discount_type', $quotation->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                        <option value="fixed" {{ old('discount_type', $quotation->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <div class="row g-2 justify-content-end align-items-center">
                                        <div class="col-12" id="discount_value_col">
                                            <div class="position-relative">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" id="discount_addon_left" style="left: 15px; display:none; font-weight: 600;">₹</span>
                                                <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control text-center calc-input shadow-none px-4" value="{{ old('discount_value', $quotation->discount_value ?? '') }}" placeholder="0" style="height: 38px;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" id="discount_addon_right" style="right: 15px; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $quotation->discount_amount ?? '0.00') }}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle" style="width: 45%;">
                                    <div class="mb-1">Tax Type</div>
                                    <select name="tax_type" id="tax_type" class="form-select shadow-none w-100">
                                        <option value="cgst_sgst" {{ old('tax_type', $quotation->tax_type ?? 'cgst_sgst') == 'cgst_sgst' ? 'selected' : '' }}>CGST + SGST</option>
                                        <option value="igst" {{ old('tax_type', $quotation->tax_type) == 'igst' ? 'selected' : '' }}>IGST</option>
                                        <option value="none" {{ old('tax_type', $quotation->tax_type) == 'none' ? 'selected' : '' }}>No Tax</option>
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <div class="row g-2 justify-content-end align-items-end" id="taxFields">
                                        <div class="col-6" id="cgst_col">
                                            <label class="form-label text-center d-block small fw-bold mb-1">CGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="0.01" name="cgst_percentage" id="cgst_percentage" class="form-control text-center calc-input px-3" value="{{ old('cgst_percentage', $quotation->cgst_percentage ?? 9) }}" style="height: 38px;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6" id="sgst_col">
                                            <label class="form-label text-center d-block small fw-bold mb-1">SGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="0.01" name="sgst_percentage" id="sgst_percentage" class="form-control text-center calc-input px-3" value="{{ old('sgst_percentage', $quotation->sgst_percentage ?? 9) }}" style="height: 38px;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-12" id="igst_col" style="display:none;">
                                            <label class="form-label text-center d-block small fw-bold mb-1">IGST</label>
                                            <div class="position-relative">
                                                <input type="number" step="0.01" name="igst_percentage" id="igst_percentage" class="form-control text-center calc-input px-3" value="{{ old('igst_percentage', $quotation->igst_percentage ?? 18) }}" style="height: 38px;">
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                        <div class="col-12" id="no_tax_col" style="display:none;">
                                            <label class="form-label text-center d-block small fw-bold mb-1">&nbsp;</label>
                                            <div class="position-relative">
                                                <input type="number" class="form-control text-center px-3" value="0" style="height: 38px; background-color: #e9ecef; cursor: not-allowed;" readonly>
                                                <span class="position-absolute top-50 translate-middle-y text-muted" style="right: 10px; font-size: 0.75rem; font-weight: 600;">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">Total Tax</td>
                                <td><input type="text" name="total_tax" id="total_tax" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('total_tax', ($quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount)) }}" style="height: 38px;"></td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">Round Off</td>
                                <td><input type="text" name="round_off" id="round_off" class="form-control calc-input text-end shadow-none px-3" readonly value="{{ old('round_off', $quotation->round_off ?? '0.00') }}" style="height: 38px;"></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Grand Total</strong></td>
                                <td><input type="text" name="grand_total" id="grand_total" class="form-control calc-input fw-bold text-end shadow-none px-3" readonly value="{{ old('grand_total', $quotation->grand_total ?? '0.00') }}" style="height: 38px; font-size: 1.1rem; color: #4A5568;"></td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 d-flex justify-content-end gap-3">
                <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 bg-white">Cancel</a>
                <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-5" style="background: linear-gradient(135deg, #8E2DE2, #4A00E0); border: none;">
                    <i class="bx bx-save me-1"></i> Save
                </button>
            </div>
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
var itemIndex = {{ count($quotation->items ?? []) }};

function formatItem(item) {
    if (!item.id) { return item.text; }
    var $el = $(item.element);
    var image = $el.data('image');
    var rate = $el.data('rate') || 0;
    var hsn = $el.data('hsn') || 'N/A';
    
    var imgHtml = image ? '<img src="' + image + '" class="rounded me-2" style="width:32px; height:32px; object-fit:cover;">' : '<div class="rounded me-2 bg-light border d-inline-block" style="width:32px; height:32px;"></div>';
    
    return $(
        '<div class="d-flex align-items-center py-1">' +
            imgHtml +
            '<div>' +
                '<div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.2;">' + item.text.split(' (')[0] + '</div>' +
                '<div class="text-muted" style="font-size: 0.75rem;">Rate: ₹' + parseFloat(rate).toFixed(2) + ' | HSN: ' + hsn + '</div>' +
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

function calculateRow(row) {
    var qty = parseFloat(row.find('.quantity-input').val()) || 0;
    var rate = parseFloat(row.find('.rate-input').val()) || 0;

    var amount = qty * rate;
    
    // We only need the base amount for the row, global tax/discount are calculated in summary
    row.find('.total-input').val(amount.toFixed(2));

    calculateSummary();
}

function calculateSummary() {
    var subtotal = 0;

    $('.item-row').each(function(){
        var total = parseFloat($(this).find('.total-input').val()) || 0;
        subtotal += total;
    });

    $('#subtotal').val(subtotal.toFixed(2));

    var discType = $('select[name="discount_type"]').val();
    var discVal = parseFloat($('#discount_value').val()) || 0;
    var discAmt = 0;
    if (discType === 'percentage') {
        discAmt = (discVal / 100) * subtotal;
    } else {
        discAmt = discVal;
    }
    $('#discount_amount').val(discAmt.toFixed(2));

    var afterDiscount = subtotal - discAmt;
    
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

    $('#total_tax').val(totalTax.toFixed(2));

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

        row.find('.hsn-input').val(data.hsn || data.hsn_code || '');
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
        templateResult: formatItem,
        templateSelection: function(item) {
            if (!item.id) return item.text;
            return item.text.split(' (')[0];
        }
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

        row.find('.rate-input').val(rate);
        row.find('.unit-input').val(unit);
        row.find('.hsn-input').val(hsn);
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

    $('select[name="discount_type"]').on('change', function(){
        var type = $(this).val();
        if (type === 'percentage') {
            $('#discount_addon_left').hide();
            $('#discount_addon_right').show();
        } else {
            $('#discount_addon_left').show();
            $('#discount_addon_right').hide();
        }
        calculateSummary();
    });
    $('select[name="discount_type"]').trigger('change');

    $('#discount_value').on('input', function(){
        calculateSummary();
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

    var savedTaxType = '{{ old('tax_type', $quotation->tax_type ?? 'cgst_sgst') }}';
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

    @if(($quotation->items ?? null) && count($quotation->items) > 0)
        var existingItems = @json($quotation->items);
        $.each(existingItems, function(idx, item){
            addItemRow(item);
        });
    @endif
});
</script>
@endsection
