<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\ServiceImageController;
use App\Http\Controllers\AunthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate',[AunthenticationController::class,'authenticate']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['middleware'=>['auth:sanctum']],function(){
// protected routes
Route::get('dashboard',[DashboardController::class,'index']);
// logouts
Route::get('logout',[AunthenticationController::class,'logout']);

// Service routes
Route::post('services',[ServiceController::class,'store']);
Route::get('services',[ServiceController::class,'index']);

//serviceimage Routes
// store
Route::post('service-images',[ServiceImageController::class,'store']);
});
