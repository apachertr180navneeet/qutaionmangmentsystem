@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Upcoming Follow-ups</h5>
        </div>
        <div class="card-body">
            @if($followUps->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($followUps as $followUp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $followUp->quotation?->quotation_number ?? 'N/A' }}</td>
                            <td>{{ $followUp->quotation?->customer?->company_name ?? 'N/A' }}</td>
                            <td>{{ date('d M, Y', strtotime($followUp->follow_up_date)) }}</td>
                            <td>{{ Str::limit($followUp->notes, 50) }}</td>
                            <td><span class="badge bg-warning">{{ ucfirst($followUp->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted mb-0">No upcoming follow-ups.</p>
            @endif
        </div>
    </div>
</div>
@endsection
