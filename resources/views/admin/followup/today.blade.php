@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Today's Follow-ups</h5>
            <span class="badge bg-primary">{{ now()->format('d M, Y') }}</span>
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
                            <th>Time</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($followUps as $followUp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $followUp->quotation?->quotation_number ?? 'N/A' }}</td>
                            <td>{{ $followUp->quotation?->customer?->company_name ?? 'N/A' }}</td>
                            <td>{{ $followUp->follow_up_time ? date('h:i A', strtotime($followUp->follow_up_time)) : '--' }}</td>
                            <td>{{ Str::limit($followUp->notes, 50) }}</td>
                            <td>
                                @php $fbadge = $followUp->status == 'pending' ? 'warning' : ($followUp->status == 'completed' ? 'success' : 'danger'); @endphp
                                <span class="badge bg-{{ $fbadge }}">
                                    {{ ucfirst($followUp->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editFollowUp({{ $followUp->id }})">Edit</button>
                                <button class="btn btn-sm btn-success" onclick="markCompleted({{ $followUp->id }})">Complete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted mb-0">No follow-ups scheduled for today.</p>
            @endif
        </div>
    </div>
</div>
@endsection
