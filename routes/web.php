<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/test-pdf', function () {
//     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Integrasi PDF Berhasil!</h1><p>Ini adalah file PDF yang digenerate oleh sistem PGS.</p>');
//     return $pdf->stream('test.pdf');
// })->middleware(['auth'])->name('test.pdf');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/catalog', [OrderController::class, 'index'])->name('orders.catalog');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}/download-letter', [OrderController::class, 'downloadLetter'])->name('orders.download-letter');
    Route::get('/orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
    Route::post('/orders/{order}/upload-signed-letter', [OrderController::class, 'uploadSignedLetter'])->name('orders.upload-signed-letter');
    Route::post('/orders/{order}/upload-payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.upload-payment-proof');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [AdminController::class, 'index'])->name('admin.orders.index');
    Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');
    Route::post('/orders/{order}/set-schedule', [AdminController::class, 'setSchedule'])->name('admin.orders.set-schedule');
    Route::patch('/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    
    Route::get('/finance', [AdminController::class, 'financeIndex'])->name('admin.finance.index');
    Route::post('/finance/download-report', [AdminController::class, 'downloadFinanceReport'])->name('admin.finance.download-report');
    Route::post('/finance/{finance}/update-cogs', [AdminController::class, 'updateCogsDetailed'])->name('admin.finance.update-cogs');
    
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('admin.activity-logs.index');
    
    // Manage Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

    Route::post('/documents/{document}/verify', [AdminController::class, 'verifyDocument'])->name('admin.documents.verify');
    Route::post('/documents/{document}/unverify', [AdminController::class, 'unverifyDocument'])->name('admin.documents.unverify');
    Route::post('/payments/{payment}/verify', [AdminController::class, 'verifyPayment'])->name('admin.payments.verify');

    // Manage Catalog
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->names('admin.services');

    // Manage Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');

    // Manage COGS Items & Facilitators
    Route::resource('cogs-items', \App\Http\Controllers\Admin\CogsItemController::class)->names('admin.cogs-items');
    Route::resource('facilitators', \App\Http\Controllers\Admin\FacilitatorController::class)->names('admin.facilitators');
});

require __DIR__.'/auth.php';
