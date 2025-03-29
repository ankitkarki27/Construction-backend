<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\ServiceImageController;
use App\Http\Controllers\AunthenticationController;
use App\Http\Controllers\front\ServiceController as FrontServiceController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes (No Auth Required)
Route::post('authenticate', [AunthenticationController::class, 'authenticate']); 
Route::get('get-services', [FrontServiceController::class, 'index']);  
Route::get('latest-services', [FrontServiceController::class, 'newservices']);  

// Protected Routes (Only Authenticated Users Can Access)
Route::group(['middleware' => ['auth:sanctum']], function() {  

    Route::get('dashboard', [DashboardController::class, 'index']);  
    Route::get('logout', [AunthenticationController::class, 'logout']);  

    // Service Management Routes (For Admin Only)
    Route::post('services', [ServiceController::class, 'store']);  
    Route::get('services', [ServiceController::class, 'index']);  
    Route::put('services/{id}', [ServiceController::class, 'update']);  
    Route::get('services/{id}', [ServiceController::class, 'show']);  
    Route::delete('services/{id}', [ServiceController::class, 'destroy']); 

    // Service Image Management (Admin Only)
    Route::post('service-images', [ServiceImageController::class, 'store']);  
});
