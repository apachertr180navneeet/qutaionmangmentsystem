@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0">
            <span class="text-primary fw-light">Reports /</span> Item Wise Report
        </h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> All Reports
        </a>
    </div>

    <div class="card filter-card no-print mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.item_wise') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Item <span class="text-danger">*</span></label>
                    <select name="item_id" class="form-select select2" required>
                        <option value="">Select Item</option>
                        @foreach($items as $itm)
                            <option value="{{ $itm->id }}" {{ request('item_id') == $itm->id ? 'selected' : '' }}>
                                {{ $itm->name }} @if($itm->sku)({{ $itm->sku }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search me-1"></i> Generate Report</button>
                    @if(request('item_id'))
                        <a href="{{ route('admin.reports.item_wise') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotationItems) && $quotationItems->count() > 0)
    <div class="card report-result-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="card-title mb-0">
                    <strong>Results for:</strong> {{ $item->name ?? 'N/A' }} @if($item && $item->sku)<span class="text-muted">({{ $item->sku }})</span>@endif
                </h5>
                <small class="text-muted">Found {{ $quotationItems->total() }} record(s)</small>
            </div>
            <div class="report-actions no-print">
                <button type="button" class="btn-action btn-report-print" onclick="window.print()">
                    <i class="bx bx-printer"></i>
                    <span>Print</span>
                </button>
                <a href="{{ route('admin.reports.export.pdf', ['report_type' => 'item_wise', 'item_id' => request('item_id')]) }}" class="btn-action btn-report-pdf" target="_blank">
                    <i class="bx bxs-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('admin.reports.export.excel', ['report_type' => 'item_wise', 'item_id' => request('item_id')]) }}" class="btn-action btn-report-excel">
                    <i class="bx bx-spreadsheet"></i>
                    <span>Excel</span>
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Print-only header -->
            <div class="print-only p-3 mb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold">{{ config('app.name', 'Quotation Management System') }}</h4>
                        <h5 class="mb-1 text-primary">Item Wise Quotation Report</h5>
                        <p class="mb-0 text-muted"><strong>Item:</strong> {{ $item->name ?? 'N/A' }} @if($item && $item->sku)({{ $item->sku }})@endif</p>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong>Printed On:</strong> {{ date('d-m-Y H:i') }}</div>
                        <div><strong>Total Records:</strong> {{ $quotationItems->total() }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 18%;">Quotation No</th>
                            <th style="width: 27%;">Customer</th>
                            <th style="width: 14%;">Date</th>
                            <th style="width: 12%;" class="text-end">Quantity</th>
                            <th style="width: 12%;" class="text-end">Rate</th>
                            <th style="width: 12%;" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotationItems as $key => $qi)
                        <tr>
                            <td>{{ $quotationItems->firstItem() + $key }}</td>
                            <td class="fw-semibold">
                                @if($qi->quotation)
                                    <a href="{{ route('admin.quotations.show', $qi->quotation->id) }}" class="no-print text-primary">{{ $qi->quotation->quotation_number }}</a>
                                    <span class="print-only">{{ $qi->quotation->quotation_number }}</span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $qi->quotation->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $qi->quotation->date ? date('d-m-Y', strtotime($qi->quotation->date)) : ($qi->quotation->created_at ? date('d-m-Y', strtotime($qi->quotation->created_at)) : 'N/A') }}</td>
                            <td class="text-end">{{ formatNumber($qi->quantity) }}</td>
                            <td class="text-end">₹{{ formatNumber($qi->rate) }}</td>
                            <td class="text-end fw-semibold">₹{{ formatNumber($qi->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total (All Pages):</strong></td>
                            <td class="text-end"><strong>{{ formatNumber($totalQuantity) }}</strong></td>
                            <td></td>
                            <td class="text-end"><strong>₹{{ formatNumber($totalAmount) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3 mb-3 no-print">
                {{ $quotationItems->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @elseif(request('item_id'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">
            <i class="bx bx-info-circle fs-1 text-muted d-block mb-2"></i>
            No quotation records found for this item.
        </div>
    </div>
    @endif
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
