<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Integrasi PDF Berhasil!</h1><p>Ini adalah file PDF yang digenerate oleh sistem PGS.</p>');
    return $pdf->stream('test.pdf');
})->middleware(['auth'])->name('test.pdf');

Route::middleware('auth')->group(function () {
    Route::get('/catalog', [OrderController::class, 'index'])->name('orders.catalog');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
    Route::get('/orders/{order}/download-letter', [OrderController::class, 'downloadLetter'])->name('orders.download-letter');
    Route::get('/orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
    Route::post('/orders/{order}/upload-signed-letter', [OrderController::class, 'uploadSignedLetter'])->name('orders.upload-signed-letter');
    Route::post('/orders/{order}/upload-payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.upload-payment-proof');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [AdminController::class, 'index'])->name('admin.orders.index');
    Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');
    Route::post('/orders/{order}/set-schedule', [AdminController::class, 'setSchedule'])->name('admin.orders.set-schedule');
    Route::patch('/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');
    
    Route::get('/finance', [AdminController::class, 'financeIndex'])->name('admin.finance.index');
    Route::post('/finance/{finance}/update-cogs', [AdminController::class, 'updateCogsDetailed'])->name('admin.finance.update-cogs');
    
    Route::post('/documents/{document}/verify', [AdminController::class, 'verifyDocument'])->name('admin.documents.verify');
    Route::post('/documents/{document}/unverify', [AdminController::class, 'unverifyDocument'])->name('admin.documents.unverify');
    Route::post('/payments/{payment}/verify', [AdminController::class, 'verifyPayment'])->name('admin.payments.verify');

    // Manage Catalog
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->names('admin.services');
});

require __DIR__.'/auth.php';
