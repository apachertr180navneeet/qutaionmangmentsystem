@extends('admin.layouts.app')
@section('style')
<style>
.detail-label { font-weight: 600; color: #555; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Quotation Details</span>
    </h5>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Quotation #{{ $quotation->quotation_number }}</strong>
            @php
                $statusClasses = ['draft' => 'bg-label-secondary', 'sent' => 'bg-label-primary', 'approved' => 'bg-label-success', 'expired' => 'bg-label-warning', 'rejected' => 'bg-label-danger'];
            @endphp
            <span class="badge {{ $statusClasses[$quotation->status] ?? 'bg-label-secondary' }} fs-6">{{ ucfirst($quotation->status) }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <p><span class="detail-label">Customer:</span><br>{{ $quotation->customer->company_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p><span class="detail-label">Date:</span><br>{{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p><span class="detail-label">Valid Until:</span><br>{{ $quotation->valid_until ? date('d-m-Y', strtotime($quotation->valid_until)) : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Items</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>HSN</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Disc%</th>
                            <th>Disc Amt</th>
                            <th>Taxable Value</th>
                            <th>CGST%</th>
                            <th>CGST Amt</th>
                            <th>SGST%</th>
                            <th>SGST Amt</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->item->name ?? $item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->hsn_code ?? '' }}</td>
                            <td>{{ $item->unit ?? '' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->rate, 2) }}</td>
                            <td>{{ $item->discount_percentage }}</td>
                            <td>{{ number_format($item->discount_amount, 2) }}</td>
                            <td>{{ number_format($item->taxable_value, 2) }}</td>
                            <td>{{ $item->cgst_percentage }}</td>
                            <td>{{ number_format($item->cgst_amount, 2) }}</td>
                            <td>{{ $item->sgst_percentage }}</td>
                            <td>{{ number_format($item->sgst_amount, 2) }}</td>
                            <td>{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="14" class="text-center text-muted py-3">No items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Summary</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if($quotation->notes)
                    <p><span class="detail-label">Notes:</span><br>{{ $quotation->notes }}</p>
                    @endif
                    @if($quotation->terms_conditions)
                    <p><span class="detail-label">Terms & Conditions:</span><br>{{ $quotation->terms_conditions }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr><td>Subtotal</td><td class="text-end">{{ number_format($quotation->subtotal, 2) }}</td></tr>
                            @if($quotation->discount_amount > 0)
                            <tr>
                                <td>Discount ({{ $quotation->discount_type == 'percentage' ? $quotation->discount_value.'%' : 'Fixed' }})</td>
                                <td class="text-end">-{{ number_format($quotation->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr><td>Total Tax</td><td class="text-end">{{ number_format($quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount, 2) }}</td></tr>
                            <tr><td>Round Off</td><td class="text-end">{{ number_format($quotation->round_off, 2) }}</td></tr>
                            <tr class="table-active"><td><strong>Grand Total</strong></td><td class="text-end"><strong>{{ number_format($quotation->grand_total, 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-primary"><i class="bx bx-arrow-back"></i> Back to List</a>
    <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn btn-primary"><i class="bx bx-edit"></i> Edit</a>
</div>
@endsection
