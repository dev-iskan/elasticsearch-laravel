<?php

namespace App\Providers;

use App\Search\Engines\ElasticSearchEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        resolve(EngineManager::class)->extend('elasticsearch', function () {
            return new ElasticSearchEngine();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
