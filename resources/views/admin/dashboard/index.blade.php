@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card premium-dashboard-card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome to Admin Dashboard! 🎉</h5>
                            <p class="mb-4">
                                You have successfully logged into the Quotation Management System. Here you can manage your quotes, users, and overall system settings.
                            </p>
                            <a href="javascript:;" class="btn btn-sm btn-outline-light mt-2">View Quotes</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('assets/admin/img/illustrations/man-with-laptop-light.png')}}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class='bx bx-file bx-md text-primary mb-3'></i>
                            <span class="fw-semibold d-block mb-1">Total Quotes</span>
                            <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class='bx bx-user bx-md text-success mb-3'></i>
                            <span class="fw-semibold d-block mb-1">Total Users</span>
                            <h3 class="card-title mb-2">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
