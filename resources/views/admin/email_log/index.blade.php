@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Email Logs</span>
    </h5>
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('admin.email_logs.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
                <select name="status" class="form-select" style="min-width:150px;">
                    <option value="">All Status</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="opened" {{ request('status') == 'opened' ? 'selected' : '' }}>Opened</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                @if(request('status'))
                    <a href="{{ route('admin.email_logs.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emailLogs as $key => $log)
                        <tr>
                            <td>{{ $emailLogs->firstItem() + $key }}</td>
                            <td>{{ $log->quotation->quotation_number ?? 'N/A' }}</td>
                            <td>{{ $log->recipient_email }} ({{ $log->recipient_name }})</td>
                            <td>{{ $log->subject }}</td>
                            <td>
                                @php
                                    $statusClasses = ['sent' => 'bg-label-primary', 'failed' => 'bg-label-danger', 'opened' => 'bg-label-success'];
                                @endphp
                                <span class="badge {{ $statusClasses[$log->status] ?? 'bg-label-secondary' }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td>{{ $log->sent_at ? date('d-m-Y H:i', strtotime($log->sent_at)) : 'N/A' }}</td>
                            <td class="text-center table-actions">
                                <span class="text-muted">--</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No email logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $emailLogs->links() }}
        </div>
    </div>
</div>
@endsection
