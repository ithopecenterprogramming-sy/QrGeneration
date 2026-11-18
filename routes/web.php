<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\BackupController;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::prefix('qr')->name('qr.')->group(function () {

    Route::get('/', [QrCodeController::class, 'index'])
        ->name('index');

    Route::get('/create', [QrCodeController::class, 'create'])
        ->name('create');

    Route::post('/generate', [QrCodeController::class, 'store'])
        ->name('store');

    Route::get('/batches', [QrCodeController::class, 'batches'])
        ->name('batches');

    Route::get('/batch/{batch}/print', [QrCodeController::class, 'print'])
        ->name('print');

    Route::get('/{qrCode}', [QrCodeController::class, 'show'])
        ->name('show');
});

Route::prefix('backup')->name('backup.')->group(function () {

    Route::get('/', [BackupController::class, 'index'])
        ->name('index');

    Route::get('/download', [BackupController::class, 'download'])
        ->name('download');

    Route::post('/restore', [BackupController::class, 'restore'])
        ->name('restore');
});