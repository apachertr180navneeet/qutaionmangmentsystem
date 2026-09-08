@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0">
            <span class="text-primary fw-light">Reports /</span> Status Wise Report
        </h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> All Reports
        </a>
    </div>

    <div class="card filter-card no-print mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.status_wise') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search me-1"></i> Generate Report</button>
                    @if(request('status'))
                        <a href="{{ route('admin.reports.status_wise') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotations) && $quotations->count() > 0)
    <div class="card report-result-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="card-title mb-0">
                    <strong>Results for:</strong> <span class="badge bg-label-primary fs-6">{{ ucfirst(request('status')) }}</span>
                </h5>
                <small class="text-muted">Found {{ $quotations->total() }} quotation(s)</small>
            </div>
            <div class="d-flex gap-2 no-print">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Print
                </button>
                <a href="{{ route('admin.reports.export.pdf', ['report_type' => 'status_wise', 'status' => request('status')]) }}" class="btn btn-danger btn-sm" target="_blank">
                    <i class="bx bxs-file-pdf me-1"></i> PDF
                </a>
                <a href="{{ route('admin.reports.export.excel', ['report_type' => 'status_wise', 'status' => request('status')]) }}" class="btn btn-success btn-sm">
                    <i class="bx bx-file me-1"></i> Excel
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Print-only header -->
            <div class="print-only p-3 mb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold">{{ config('app.name', 'Quotation Management System') }}</h4>
                        <h5 class="mb-1 text-primary">Status Wise Quotation Report</h5>
                        <p class="mb-0 text-muted"><strong>Status Filter:</strong> {{ ucfirst(request('status')) }}</p>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong>Printed On:</strong> {{ date('d-m-Y H:i') }}</div>
                        <div><strong>Total Records:</strong> {{ $quotations->total() }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">Quotation No</th>
                            <th style="width: 30%;">Customer</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 15%;" class="text-end">Grand Total</th>
                            <th style="width: 15%;" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $key => $q)
                        @php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                        <tr>
                            <td>{{ $quotations->firstItem() + $key }}</td>
                            <td class="fw-semibold">
                                <a href="{{ route('admin.quotations.show', $q->id) }}" class="no-print text-primary">{{ $q->quotation_number }}</a>
                                <span class="print-only">{{ $q->quotation_number }}</span>
                            </td>
                            <td>{{ $q->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $q->date ? date('d-m-Y', strtotime($q->date)) : ($q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A') }}</td>
                            <td class="text-end fw-semibold">₹{{ formatNumber($q->grand_total) }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total (All Pages):</strong></td>
                            <td class="text-end"><strong>₹{{ formatNumber($totalGrandTotal) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3 mb-3 no-print">
                {{ $quotations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @elseif(request('status'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">
            <i class="bx bx-info-circle fs-1 text-muted d-block mb-2"></i>
            No quotations found with this status.
        </div>
    </div>
    @endif
</div>
@endsection
