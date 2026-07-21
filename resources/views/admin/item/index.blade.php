@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Item List
        </h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" id="btn-sync-images" style="border-radius: 8px;">
                <i class="bx bx-sync me-1"></i> Sync Images
            </button>
            <a href="{{ route('admin.items.import_template') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                <i class="bx bx-download me-1"></i> Template
            </a>
            <button type="button" class="btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-upload me-1"></i> Import
            </button>
            <a href="{{ route('admin.items.create') }}" class="btn-success-custom">
                <i class="bx bx-plus-circle me-1"></i> Add New Item
            </a>
        </div>
    </div>
    <div class="custom-card mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="{{ route('admin.items.index') }}" class="d-flex gap-3 flex-wrap align-items-center flex-grow-1">
                <div class="flex-grow-1" style="max-width: 100%;">
                    <input type="text" name="search" class="custom-input no-icon" placeholder="Search by name, SKU..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-gradient-primary"><i class="bx bx-search me-1"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Clear</a>
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Unit</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $item)
                        <tr>
                            <td>{{ $items->firstItem() + $key }}</td>
                            <td>
                                <div class="position-relative d-inline-block item-image-container" style="width: 44px; height: 44px;">
                                    <form class="ajax-image-form" data-id="{{ $item->id }}" enctype="multipart/form-data" style="display:none;">
                                        @csrf
                                        <input type="file" name="image" class="item-image-input" id="item-image-input-{{ $item->id }}" accept="image/*">
                                    </form>
                                    <label for="item-image-input-{{ $item->id }}" class="mb-0 cursor-pointer position-relative d-block group-image-wrapper" title="Click to change image">
                                        <img src="{{ $item->image ?? asset('assets/admin/img/avatars/1.png') }}" alt="{{ $item->name }}" class="img-thumbnail item-img-preview-{{ $item->id }}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px;">
                                        <div class="image-overlay d-flex align-items-center justify-content-center">
                                            <i class="bx bx-camera text-white fs-6"></i>
                                        </div>
                                    </label>
                                </div>
                            </td>
                            <td>{{ $item->name }}</td>
                            <td><span class="text-purple-custom">{{ $item->sku }}</span></td>
                            <td class="table-dark-text">{{ $item->unit }}</td>
                            <td class="table-dark-text">{{ number_format($item->rate, 2) }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge-custom badge-active">ACTIVE</span>
                                @else
                                    <span class="badge-custom badge-inactive">INACTIVE</span>
                                @endif
                            </td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.items.show', $item->id) }}" class="action-btn-outline btn-outline-view" title="View"><i class="bx bx-show"></i></a>
                                <a href="{{ route('admin.items.edit', $item->id) }}" class="action-btn-outline btn-outline-edit" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="action-btn-outline btn-outline-delete delete-item" data-id="{{ $item->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.items.destroy', $item->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-3">No items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center p-4">
            {{ $items->links() }}
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.items.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload CSV/Excel File</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".csv, .xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-gradient-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<style>
.group-image-wrapper {
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
}
.group-image-wrapper .image-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.45);
    opacity: 0;
    transition: opacity 0.25s ease;
    border-radius: 8px;
}
.group-image-wrapper:hover .image-overlay {
    opacity: 1;
}
</style>
<script>
$(document).ready(function(){
    $('.delete-item').on('click', function(){
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

    $(document).on('change', '.item-image-input', function() {
        var fileInput = this;
        if (!fileInput.files || !fileInput.files[0]) return;

        var form = $(fileInput).closest('form');
        var itemId = form.data('id');
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('image', fileInput.files[0]);

        var previewImg = $('.item-img-preview-' + itemId);
        var originalSrc = previewImg.attr('src');

        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while the image is updating.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ url('admin/items') }}/" + itemId + "/update-image",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    previewImg.attr('src', response.image_url);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: response.message || 'Could not update image.'
                    });
                }
            },
            error: function(xhr) {
                var message = 'An error occurred while uploading.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
        });
    });

    $('#btn-sync-images').on('click', function() {
        Swal.fire({
            title: 'Sync Product Images?',
            text: "This will search Jaquar website by product SKUs and download missing images automatically.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, Sync Now!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Syncing Product Images...',
                    text: 'Please wait while product images are being scraped and saved.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('admin.items.sync_images') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sync Completed!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Sync Failed',
                                text: response.message || 'Could not complete image sync.'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'An error occurred during sync.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
