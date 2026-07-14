@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Email Logs
        </h4>
    </div>
    <div class="custom-card mb-4 p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('admin.email_logs.index') }}" class="d-flex gap-3 flex-wrap align-items-center flex-grow-1">
                <div style="min-width: 200px;">
                    <select name="status" class="custom-select">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="opened" {{ request('status') == 'opened' ? 'selected' : '' }}>Opened</option>
                    </select>
                </div>
                <button type="submit" class="btn-gradient-primary"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                @if(request('status'))
                    <a href="{{ route('admin.email_logs.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Clear</a>
                @endif
            </form>
        </div>
    </div>
    <div class="custom-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
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
                            <td><span class="text-purple-custom">{{ $log->quotation->quotation_number ?? 'N/A' }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle">{{ strtoupper(substr($log->recipient_name ?? 'N', 0, 1)) }}</span>
                                    <span class="table-dark-text">{{ $log->recipient_email }} ({{ $log->recipient_name }})</span>
                                </div>
                            </td>
                            <td class="table-dark-text">{{ $log->subject }}</td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($log->status)) {
                                        'sent' => 'badge-sent',
                                        'failed' => 'badge-rejected',
                                        'opened' => 'badge-active',
                                        default => 'badge-draft'
                                    };
                                @endphp
                                <span class="badge-custom {{ $statusClass }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td class="table-dark-text">{{ $log->sent_at ? date('d-m-Y H:i', strtotime($log->sent_at)) : 'N/A' }}</td>
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
        <div class="d-flex justify-content-center p-4">
            {{ $emailLogs->links() }}
        </div>
    </div>
</div>
@endsection
