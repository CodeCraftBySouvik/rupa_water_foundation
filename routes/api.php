<?php
use App\Http\Controllers\Api\AuthController;

Route::prefix('souvik')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});