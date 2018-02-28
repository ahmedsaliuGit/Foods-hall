<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

use App\Models\Channel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // view()->share('channels', Channel::all());

        view()->composer('*', function($view) {
            $channels = \Cache::rememberForever('channels', function (){
                return Channel::all();
            });
            $view->with('channels', $channels);
        });

        Validator::extend('spamfree', '\App\Rules\SpamFree@passes');
        Validator::extend('recaptcha', '\App\Rules\Recaptcha@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
