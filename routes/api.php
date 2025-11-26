<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PixController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookPixController;
use App\Http\Controllers\WebhookWithdrawController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'adquirencia'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/pix/create', [PixController::class, 'create']);
    Route::post('/withdraw', [WithdrawController::class, 'create']);
});

Route::post('/webhook/pix', [WebhookPixController::class, 'handle']);
Route::post('/webhook/withdraw', [WebhookWithdrawController::class, 'handle']);
