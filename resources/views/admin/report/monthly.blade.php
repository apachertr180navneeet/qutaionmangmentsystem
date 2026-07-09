@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Monthly Report</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.monthly') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', date('m')) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotations))
    <div class="card mt-3">
        <div class="card-header"><strong>Results for {{ date('F', mktime(0, 0, 0, request('month', date('m')), 1)) }} {{ request('year', date('Y')) }}</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $key => $q)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $q->quotation_number }}</td>
                            <td>{{ $q->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A' }}</td>
                            <td>{{ number_format($q->grand_total, 2) }}</td>
                            <td>@php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                            <span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No quotations found for this month.</td></tr>
                        @endforelse
                    </tbody>
                    @if($quotations->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($quotations->sum('grand_total'), 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
