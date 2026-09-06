<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DelayRequestController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FloorRoomController;
use App\Http\Controllers\PaymentVerificationController;
use App\Http\Controllers\PgFormController;
use App\Http\Controllers\RentInvoiceController;
use App\Http\Controllers\RenterController;
use App\Http\Controllers\RenterPortalController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureRenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin() 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('renter.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Floors & Rooms
    Route::get('/floors-rooms', [FloorRoomController::class, 'index'])->name('floors-rooms.index');
    Route::post('/floors', [FloorRoomController::class, 'storeFloor'])->name('floors.store');
    Route::put('/floors/{floor}', [FloorRoomController::class, 'updateFloor'])->name('floors.update');
    Route::delete('/floors/{floor}', [FloorRoomController::class, 'destroyFloor'])->name('floors.destroy');

    Route::post('/rooms', [FloorRoomController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/rooms/{room}', [FloorRoomController::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/rooms/{room}', [FloorRoomController::class, 'destroyRoom'])->name('rooms.destroy');

    // Renters
    Route::resource('renters', RenterController::class);

    // Rent Invoices & Generation
    Route::get('/invoices', [RentInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/generate', [RentInvoiceController::class, 'generateMonthlyInvoices'])->name('invoices.generate');
    Route::get('/invoices/{invoice}', [RentInvoiceController::class, 'show'])->name('invoices.show');

    // Payment Verifications
    Route::get('/payments', [PaymentVerificationController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/approve', [PaymentVerificationController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');

    // Delay Requests
    Route::get('/delay-requests', [DelayRequestController::class, 'index'])->name('delay-requests.index');
    Route::post('/delay-requests/{delayRequest}/approve', [DelayRequestController::class, 'approve'])->name('delay-requests.approve');
    Route::post('/delay-requests/{delayRequest}/reject', [DelayRequestController::class, 'reject'])->name('delay-requests.reject');

    // Staff & Salaries
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::post('/staff/salary', [StaffController::class, 'storeSalary'])->name('staff.salary.store');
    Route::put('/staff/salary/{salary}', [StaffController::class, 'updateSalary'])->name('staff.salary.update');
    Route::delete('/staff/salary/{salary}', [StaffController::class, 'destroySalary'])->name('staff.salary.destroy');

    // Forms Published to Renters
    Route::get('/forms', [PgFormController::class, 'adminIndex'])->name('forms.index');
    Route::post('/forms', [PgFormController::class, 'storeForm'])->name('forms.store');
    Route::delete('/forms/{form}', [PgFormController::class, 'destroyForm'])->name('forms.destroy');
    Route::post('/forms/submissions/{submission}/review', [PgFormController::class, 'reviewSubmission'])->name('forms.submissions.review');

    // Expenses & Profit & Loss
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/profit-loss', [ExpenseController::class, 'profitLossReport'])->name('expenses.profit-loss');
});

// Renter Routes
Route::middleware(['auth', EnsureRenter::class])->prefix('renter')->name('renter.')->group(function () {
    Route::get('/dashboard', [RenterPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/update-profile', [RenterPortalController::class, 'updateProfileDetails'])->name('update-profile');
    Route::post('/upload-document', [RenterPortalController::class, 'uploadDocument'])->name('upload-document');
    Route::post('/forms/{form}/submit', [PgFormController::class, 'renterSubmitForm'])->name('forms.submit');
    Route::post('/pay/{invoice}', [RenterPortalController::class, 'submitPayment'])->name('pay');
    Route::post('/delay/{invoice}', [RenterPortalController::class, 'submitDelayRequest'])->name('delay');
    Route::get('/receipt/{invoice}', [RenterPortalController::class, 'printReceipt'])->name('receipt');
});
