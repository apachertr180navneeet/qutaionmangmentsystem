@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Follow-ups</span>
    </h5>
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('admin.followups.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
                <select name="status" class="form-select" style="min-width:150px;">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From date">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To date">
                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                @if(request('status') || request('from_date') || request('to_date'))
                    <a href="{{ route('admin.followups.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.followups.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Add Follow-up</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
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
                            <td>{{ $followup->quotation->quotation_number ?? 'N/A' }}</td>
                            <td>{{ $followup->quotation->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $followup->follow_up_date ? date('d-m-Y', strtotime($followup->follow_up_date)) : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClasses = ['pending' => 'bg-label-warning', 'completed' => 'bg-label-success', 'cancelled' => 'bg-label-danger'];
                                @endphp
                                <span class="badge {{ $statusClasses[$followup->status] ?? 'bg-label-secondary' }}">{{ ucfirst($followup->status) }}</span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($followup->notes, 50) }}</td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.followups.edit', $followup->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger delete-followup" data-id="{{ $followup->id }}" title="Delete"><i class="bx bx-trash"></i></button>
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
        <div class="card-footer d-flex justify-content-center">
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
