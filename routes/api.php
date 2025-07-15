<?php
use App\Http\Controllers\Api\{AuthController,AboutUsController};

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('/about-us',[AboutUsController::class, 'show']);
        Route::put('/about-us/{id}',[AboutUsController::class, 'update']);

    });
});