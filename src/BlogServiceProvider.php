<?php

namespace SynergizeFlow\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load migrations directly or allow publishing
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'synergizeflow-blog-migrations');
    }

    public function register(): void
    {
        // Bind headless action or defaults if needed
    }
}
