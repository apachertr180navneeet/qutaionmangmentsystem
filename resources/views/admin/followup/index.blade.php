@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Follow-ups
        </h4>
        <a href="{{ route('admin.followups.create') }}" class="btn-gradient-primary">
            <i class="bx bx-plus-circle me-1"></i> Add Follow-up
        </a>
    </div>
    <div class="custom-card mb-4 p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('admin.followups.index') }}" class="d-flex gap-3 flex-wrap align-items-center flex-grow-1">
                <div class="flex-grow-1" style="min-width: 140px;">
                    <select name="status" class="custom-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex-grow-1" style="min-width: 140px;">
                    <input type="date" name="from_date" class="custom-input no-icon" value="{{ request('from_date') }}" placeholder="From date">
                </div>
                <div class="flex-grow-1" style="min-width: 140px;">
                    <input type="date" name="to_date" class="custom-input no-icon" value="{{ request('to_date') }}" placeholder="To date">
                </div>
                <button type="submit" class="btn-gradient-primary"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                @if(request('status') || request('from_date') || request('to_date'))
                    <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Clear</a>
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
                            <th>Customer</th>
                            <th>Follow-up Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($followUps as $key => $followup)
                        <tr>
                            <td>{{ $followUps->firstItem() + $key }}</td>
                            <td><span class="text-purple-custom">{{ $followup->quotation->quotation_number ?? 'N/A' }}</span></td>
                            <td class="table-dark-text">{{ $followup->quotation->customer->company_name ?? 'N/A' }}</td>
                            <td class="table-dark-text">{{ $followup->follow_up_date ? date('d-m-Y', strtotime($followup->follow_up_date)) : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($followup->status)) {
                                        'pending' => 'badge-draft',
                                        'completed' => 'badge-active',
                                        'cancelled' => 'badge-inactive',
                                        default => 'badge-draft'
                                    };
                                @endphp
                                <span class="badge-custom {{ $statusClass }}">{{ ucfirst($followup->status) }}</span>
                            </td>
                            <td class="table-dark-text">{{ \Illuminate\Support\Str::limit($followup->notes, 50) }}</td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.followups.edit', $followup->id) }}" class="btn-action btn-edit" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="btn-action btn-delete delete-followup" data-id="{{ $followup->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                <form id="delete-form-{{ $followup->id }}" action="{{ route('admin.followups.destroy', $followup->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No follow-ups found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center p-4">
            {{ $followUps->links() }}
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    $('.delete-followup').on('click', function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endsection
