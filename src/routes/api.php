<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SignalController;

Route::post('/signals', [SignalController::class, 'store']);
Route::get('/signals/next', [SignalController::class, 'next']);
use App\Http\Middleware\VerifyEaToken;

Route::post('/signals/{signal}/executed', [SignalController::class, 'executed'])->middleware(VerifyEaToken::class);
