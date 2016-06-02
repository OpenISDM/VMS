<?php

namespace App\Providers;

use Validator;
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

        /**
         * Add custom validator
         */
        Validator::extend('array_index',
            'App\Validators\ArrayIndexValidator@validate');
        Validator::replacer('array_index',
            'App\Validators\ArrayIndexValidator@message');
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
