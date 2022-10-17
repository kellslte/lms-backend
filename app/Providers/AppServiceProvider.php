<?php

namespace App\Providers;

use App\Helpers\AuthHelper;
use App\Services\YoutubeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(YoutubeService::class, function(){
            return new YoutubeService();
        });

        $this->app->singleton(AuthHelper::class, function(){
            return new AuthHelper();
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
