<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Serverless (Vercel): redirect writable paths to /tmp
        if (env('VERCEL') || env('APP_ENV') === 'production') {
            // Ensure /tmp subdirectories exist
            $dirs = ['/tmp/views', '/tmp/cache', '/tmp/sessions', '/tmp/logs'];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
            }

            // Override storage paths
            config([
                'view.compiled' => '/tmp/views',
                'cache.stores.file.path' => '/tmp/cache',
                'session.files' => '/tmp/sessions',
                'logging.channels.single.path' => '/tmp/logs/laravel.log',
                'logging.channels.daily.path' => '/tmp/logs/laravel.log',
            ]);
        }
    }
}
