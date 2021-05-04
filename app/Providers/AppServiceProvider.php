<?php

namespace App\Providers;

use App\Services\BGG\Contracts\BGG;
use Exception;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(BGG::class, function () {
            $class = config('services.bgg.provider');
            if (empty($class)) {
                throw new Exception('Wrong provider class name');
            }

            return new $class();
        });
    }
}
