<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;            
use App\Http\Controllers\Admin\{AboutUsController,GalleryController,LocationController,InspectionController,UserController,ZoneController};            
            
Route::middleware('site.down')->group(function () {
Route::get('/', function () {return redirect('/dashboard');})->middleware('auth');
	Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
	Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
	Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
	Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
	Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
	Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
	Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
	Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');
	Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth');

	
	


Route::group(['middleware' => 'auth'], function () {
	Route::get('/virtual-reality', [PageController::class, 'vr'])->name('virtual-reality');
	Route::get('/rtl', [PageController::class, 'rtl'])->name('rtl');
	Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
	Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
	Route::get('/profile-static', [PageController::class, 'profile'])->name('profile-static'); 
	Route::get('/sign-in-static', [PageController::class, 'signin'])->name('sign-in-static');
	Route::get('/sign-up-static', [PageController::class, 'signup'])->name('sign-up-static'); 
	// Route::get('/{page}', [PageController::class, 'index'])->name('page');
	Route::post('logout', [LoginController::class, 'logout'])->name('logout');


	// About Us
	Route::get('/about-us', [AboutUsController::class, 'about_us'])->name('about_us.index');
	Route::post('/about-us-update/{id}', [AboutUsController::class, 'about_us_update'])->name('about_us.update');
	// Gallery
	Route::get('/gallery', [GalleryController::class, 'gallery'])->name('gallery.index');
	Route::post('/gallery-store', [GalleryController::class, 'store'])->name('gallery.store');
	Route::post('/gallery/{gallery}/{index}', [GalleryController::class, 'deleteImage'])->name('gallery.image.delete');
	//User
	Route::get('/user', [UserController::class, 'index'])->name('user.index');
	Route::get('/user-create-form', [UserController::class, 'create'])->name('user.create');
	Route::post('/user-store', [UserController::class, 'store'])->name('user.store');
	Route::get('/user-edit-form/{id}', [UserController::class, 'edit'])->name('user.edit');
	Route::post('/user-update', [UserController::class, 'update'])->name('user.update');
	
	// Location
	Route::get('/location', [LocationController::class, 'location'])->name('location.index');
	Route::get('/location-create-form', [LocationController::class, 'location_create_form'])->name('location.create');
	Route::post('/location-store', [LocationController::class, 'location_store'])->name('location.store');
	Route::get('/location-edit-form/{id}', [LocationController::class, 'location_edit_form'])->name('location.edit');
	Route::post('/location-update', [LocationController::class, 'location_update'])->name('location.update');
	Route::get('/show/{id}', [LocationController::class, 'show'])->name('location.show');
	
	// Inspection
	Route::prefix('inspection') ->name('inspection.')->group(function () {
		Route::get('/', [InspectionController::class, 'index'])->name('index');
		Route::get('/create', [InspectionController::class, 'create'])->name('create');
		Route::post('/store', [InspectionController::class, 'store'])->name('store');
		Route::get('/edit-form/{id}', [InspectionController::class, 'edit'])->name('edit');
		Route::post('/update', [InspectionController::class, 'update'])->name('update');

		// gallery
		Route::get('/galleries/{inspection_id}', [InspectionController::class, 'galleryIndex'])->name('galleries.list');
		Route::post('/galleries/store', [InspectionController::class, 'galleryStore'])->name('galleries.Store');
		Route::get('/galleries/edit/{id}', [InspectionController::class, 'galleryEdit'])->name('galleries.edit');
		Route::post('/galleries/update', [InspectionController::class, 'galleryUpdate'])->name('galleries.update');
	});

	// Zone management
	Route::prefix('zone') ->name('zone.')->group(function () {
		Route::get('/', [ZoneController::class, 'index'])->name('index');
		Route::get('/create', [ZoneController::class, 'create'])->name('create');
		Route::post('/store', [ZoneController::class, 'store'])->name('store');
		Route::get('/edit-form/{id}', [ZoneController::class, 'edit'])->name('edit');
		Route::post('/update', [ZoneController::class, 'update'])->name('update');
		Route::post('/toggle-status/{id}', [ZoneController::class, 'toggleStatus'])->name('toggleStatus');
		Route::get('/locations/{id}', [ZoneController::class, 'getLocations'])->name('getLocations');

		// zone wise location
		Route::get('/location', [ZoneController::class, 'zoneWiseLocationIndex'])->name('location.index');
		Route::get('/location/sample-csv', [ZoneController::class, 'downloadSampleCsv'])->name('location.sample');
		Route::post('/location/import', [ZoneController::class, 'import'])->name('location.import');
		Route::post('/location/create', [ZoneController::class, 'zoneWiseLocationStore'])->name('location.store');
		Route::post('/location/update/{id}', [ZoneController::class, 'zoneWiseLocationUpdate'])->name('location.update');
		Route::post('/location/toggle-status/{id}', [ZoneController::class, 'zoneWiseLocationStatus'])->name('location.status');
		Route::post('/location/delete/{id}', [ZoneController::class, 'zoneWiseLocationDelete'])->name('location.delete');
		
		// zone wise employee
		Route::get('/employee', [ZoneController::class, 'zoneWiseEmployeeIndex'])->name('employee.index');
		Route::post('/employee/store', [ZoneController::class, 'zoneWiseEmployeeStore'])->name('employee.store');
		Route::post('/employee/toggle-status/{id}', [ZoneController::class, 'zoneWiseEmployeeStatus'])->name('employee.status');
	

	});
	
});
});