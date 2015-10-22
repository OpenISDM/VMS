<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Add JSON Web Tokens (JWT)
         * Reference: https://github.com/dingo/api/wiki/Authentication#json-web-tokens-jwt
         */
        app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
           return new \Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('stringUtil', function () {
            return new \App\Utils\StringUtil;
        });
    }
}
