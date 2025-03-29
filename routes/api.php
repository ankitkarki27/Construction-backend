<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\ServiceImageController;
use App\Http\Controllers\AunthenticationController;
use App\Http\Controllers\front\ServiceController as FrontServiceController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate',[AunthenticationController::class,'authenticate']);

Route::get('services',[FrontServiceController::class,'index']);
Route::get('latest-services',[FrontServiceController::class,'newservices']);




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
Route::put('services/{id}',[ServiceController::class,'update']);
Route::get('services/{id}',[ServiceController::class,'show']);
Route::delete('services/{id}',[ServiceController::class,'destroy']);

//serviceimage Routes
// store
Route::post('service-images',[ServiceImageController::class,'store']);
});
