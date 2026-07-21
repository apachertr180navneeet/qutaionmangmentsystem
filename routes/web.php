<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanySettingController;

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::get('login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post')->middleware('throttle:5,1');
    Route::get('forget-password', [AdminAuthController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('forget-password', [AdminAuthController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
    Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [AdminAuthController::class, 'submitResetPasswordForm'])->name('reset.password.post');

    Route::middleware(['admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');
        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');
        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        // Customers
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/import-template', [CustomerController::class, 'downloadTemplate'])->name('import_template');
            Route::post('/import', [CustomerController::class, 'import'])->name('import');
            Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // Items
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/', [ItemController::class, 'store'])->name('store');
            Route::get('/import-template', [ItemController::class, 'downloadTemplate'])->name('import_template');
            Route::post('/import', [ItemController::class, 'import'])->name('import');
            Route::post('/sync-images', [ItemController::class, 'syncImages'])->name('sync_images');
            Route::post('/{id}/update-image', [ItemController::class, 'updateImage'])->name('update_image');
            Route::get('/search/ajax', [ItemController::class, 'search'])->name('search.ajax');
            Route::get('/{id}', [ItemController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ItemController::class, 'update'])->name('update');
            Route::delete('/{id}', [ItemController::class, 'destroy'])->name('destroy');
        });

        // Quotations
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('index');
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
            Route::get('/{id}', [QuotationController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{id}', [QuotationController::class, 'update'])->name('update');
            Route::delete('/{id}', [QuotationController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
            Route::post('/{id}/status', [QuotationController::class, 'updateStatus'])->name('status');
            Route::get('/{id}/pdf', [QuotationController::class, 'pdf'])->name('pdf');

            Route::post('/{id}/email', [QuotationController::class, 'email'])->name('email');
        });

        // Follow-ups
        Route::prefix('follow-ups')->name('followups.')->group(function () {
            Route::get('/', [FollowUpController::class, 'index'])->name('index');
            Route::get('/create', [FollowUpController::class, 'create'])->name('create');
            Route::post('/', [FollowUpController::class, 'store'])->name('store');
            Route::get('/today', [FollowUpController::class, 'today'])->name('today');
            Route::get('/upcoming', [FollowUpController::class, 'upcoming'])->name('upcoming');
            Route::get('/{id}', [FollowUpController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [FollowUpController::class, 'edit'])->name('edit');
            Route::put('/{id}', [FollowUpController::class, 'update'])->name('update');
            Route::delete('/{id}', [FollowUpController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/customer-wise', [ReportController::class, 'customerWise'])->name('customer_wise');
            Route::get('/date-wise', [ReportController::class, 'dateWise'])->name('date_wise');
            Route::get('/status-wise', [ReportController::class, 'statusWise'])->name('status_wise');
            Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
            Route::get('/item-wise', [ReportController::class, 'itemWise'])->name('item_wise');
            Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        });

        // Company Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [CompanySettingController::class, 'index'])->name('index');
            Route::post('/', [CompanySettingController::class, 'update'])->name('update');
        });

        // Email Logs
        Route::prefix('email-logs')->name('email_logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmailLogController::class, 'index'])->name('index');
        });
    });
});



