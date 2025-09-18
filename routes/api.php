<?php
use App\Http\Controllers\Api\{AuthController,AboutUsController,LocationController,GalleryController,InspectionController,ComplaintDataStore};

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/user-store', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('/about-us',[AboutUsController::class, 'show']);
        Route::put('/about-us/{id}',[AboutUsController::class, 'update']);

	    Route::get('/location', [LocationController::class, 'location']);
        Route::get('/location_details/{id}', [LocationController::class, 'details']);
	    Route::get('/gallery', [GalleryController::class, 'index']);
        Route::post('/inspections', [InspectionController::class, 'store']);
        Route::post('/inspections/gallery', [InspectionController::class, 'inspectionGalleryStore']);
        Route::get('/inspections/{location_id}/{checked_by}/{checked_date}/status',
           [InspectionController::class, 'inspectionStatus']);
            
	    Route::get('/zone-wise-location', [LocationController::class, 'getUserAssignedZonesLocations']);
        Route::post('/complaint-data-store', [ComplaintDataStore::class, 'complaintInfoStore']);
    });
});