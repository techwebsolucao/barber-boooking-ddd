<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Interfaces\Http\Controllers\AppointmentController;
use App\Interfaces\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/appointments', [AppointmentController::class, 'store']);
Route::post('/webhooks/payment', [WebhookController::class, 'handlePayment']);
