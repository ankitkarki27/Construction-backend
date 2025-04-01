<?php

use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\ServiceImageController;
use App\Http\Controllers\admin\TeamController;
use App\Http\Controllers\admin\TestimonialController;
use App\Http\Controllers\AunthenticationController;

// for front page displaying
use App\Http\Controllers\front\ServiceController as FrontServiceController;
use App\Http\Controllers\front\ProjectController as FrontProjectController;


use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes (No Auth Required)
Route::post('authenticate', [AunthenticationController::class, 'authenticate']); 

// service routes to show services in home page(No Auth req: so public route)
Route::get('get-services', [FrontServiceController::class, 'index']);  
// Route::get('services/:id', [FrontServiceController::class, 'service']);  
// Route::get('/services/{identifier}', [FrontServiceController::class, 'service']); // Update here to use 'service' method for both slug and ID
Route::get('/services/{slug}', [FrontServiceController::class, 'serviceBySlug']);
Route::get('/services/{slug}', [FrontServiceController::class, 'serviceBySlug']);
Route::get('latest-services', [FrontServiceController::class, 'newservices']);  

Route::get('get-projects', [FrontProjectController::class, 'index']);  
Route::get('latest-projects', [FrontProjectController::class, 'newprojects']);  

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

    // For Projects
    Route::post('projects', [ProjectController::class, 'store']);  
    Route::get('projects', [ProjectController::class, 'index']); 
    Route::get('projects/{id}', [ProjectController::class, 'show']);  
    Route::put('projects/{id}', [ProjectController::class, 'update']);  
    Route::delete('projects/{id}', [ProjectController::class, 'destroy']); 

    // For Blogs(admin only)
    Route::post('blogs', [BlogController::class, 'store']);  
    Route::get('blogs', [BlogController::class, 'index']); 
    Route::get('blogs/{id}', [BlogController::class, 'show']);  
    Route::put('blogs/{id}', [BlogController::class, 'update']);  
    Route::delete('blogs/{id}', [BlogController::class, 'destroy']); 

    // For testimonials(admin only)
    Route::post('testimonials', [TestimonialController::class, 'store']);  
    Route::get('testimonials', [TestimonialController::class, 'index']); 
    Route::get('testimonials/{id}', [TestimonialController::class, 'show']);  
    Route::put('testimonials/{id}', [TestimonialController::class, 'update']);  
    Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']); 

     // For teams(admin only)
     Route::post('teams', [TeamController::class, 'store']);  
     Route::get('teams', [TeamController::class, 'index']); 
     Route::get('teams/{id}', [TeamController::class, 'show']);  
     Route::put('teams/{id}', [TeamController::class, 'update']);  
     Route::delete('teams/{id}', [TeamController::class, 'destroy']); 
});
