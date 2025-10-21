<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\PublicVideoController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class,'login']);

Route::middleware(['auth:api','role:super-admin'])->prefix('super-admin')->group(function(){
            Route::resource('users',UserController::class);
            Route::resource('role',RoleController::class);
            Route::resource('permission',PermissionController::class);
            Route::resource('category',CategoryController::class);
            Route::resource('subcategory',SubCateoryController::class);
            Route::resource('collection',CollectionController::class);
});

Route::middleware(['auth:api','role:admin'])->prefix('admin')->group(function(){
    Route::apiResource('categories', Admin\CategoryController::class);
    Route::apiResource('videos', Admin\VideoController::class);
});

    // API 1: Get all categories
    Route::get('/categories', [PublicVideoController::class, 'getCategories']);
    
    // API 2: Get single category details
    Route::get('/categories/{uuid}', [PublicVideoController::class, 'getCategoryDetails']);
    
    // API 3: Get videos under a category
    Route::get('/categories/{uuid}/videos', [PublicVideoController::class, 'getCategoryVideos']);
    
    // API 4: Get all public videos
    Route::get('/videos', [PublicVideoController::class, 'getAllVideos']);
    
    // API 5: Get single video details
    Route::get('/videos/{uuid}', [PublicVideoController::class, 'getVideoDetails']);

