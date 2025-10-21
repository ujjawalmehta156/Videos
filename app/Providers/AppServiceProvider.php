<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Spatie\MediaLibrary\MediaLibraryFileAdder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
            config(['media-library.max_file_size' => 6 * 1024 * 1024 * 1024]);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
         ini_set('upload_max_filesize', '6000M');
    ini_set('post_max_size', '6000M');
    ini_set('memory_limit', '8000M');
    }
}
