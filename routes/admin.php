<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SubCateoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\Collection;
use FFMpeg\FFMpeg as FFMpegFactory ;
Route::get('/hls-player', function () {
    return view('hls_player');
});

Route::get('/test-hls', function() {
    $ffmpeg = FFMpegFactory::create([
        'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
        'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
        'timeout'          => 3600, // optional
        'ffmpeg.threads'   => 2,    // optional
    ]);
    echo "FFmpeg exists? " . (file_exists(env('FFMPEG_PATH')) ? 'Yes' : 'No');    return 'FFMpeg instance created successfully!';
});
Route::get('/clear-cache', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');

    return "✅ All caches cleared successfully!";
});

Route::prefix('super-admin') // URL prefix
    ->middleware(['auth','role:super-admin']) // Middleware
    ->name('super-admin.') // Route name prefix
    ->group(function(){
        Route::get('/dashboard', [ProfileController::class,'dashboard'])->name('dashboard'); // URL: /super-admin/dashboard, name: super-admin.dashboard
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::resource('user',UserController::class);
            Route::resource('role',RoleController::class);
            Route::resource('permission',PermissionController::class);
            Route::resource('category',CategoryController::class);
            Route::resource('subcategory',SubCateoryController::class);
            Route::resource('collection',CollectionController::class);
            Route::get('get-subcategories/{id}', [CategoryController::class, 'getSubcategories'])->name('admin.get-subcategories');

       
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard',[ProfileController::class,'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['role:admin'])->group(function(){
        Route::resource('category',CategoryController::class);
        Route::resource('subcategory',SubCateoryController::class);
        Route::resource('collection',CollectionController::class);
        Route::get('get-subcategories/{id}', [CategoryController::class, 'getSubcategories'])->name('admin.get-subcategories');
    });
});
