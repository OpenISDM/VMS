<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\AbstractException;
use App\Http\Responses\Responses;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiErrorHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Register custom error response in Dingo API.
     * Reference: https://github.com/dingo/api/wiki/Errors-And-Error-Responses#custom-exception-responses
     *
     * @return void
     */
    public function boot()
    {
        app('Dingo\Api\Exception\Handler')->register(function (AbstractException $e) {
            return response()->apiJsonError(
                    $e->getMessage(),
                    $e->getErrors(),
                    $e->getStatusCode());
        });
        
        app('Dingo\Api\Exception\Handler')->register(function (TokenExpiredException $e) {
            $message = 'Token expired';
            $error = new Error('token_expired');
            $statusCode = $e->getStatusCode();

            return response()->apiJsonError($message, $error, $statusCode);
        });

        app('Dingo\Api\Exception\Handler')->register(function (TokenInvalidException $e) {
            $message = 'Token invalid';
            $error = new Error('token_invalid');
            $statusCode = $e->getStatusCode();

            return response()->apiJsonError($message, $error, $statusCode);
        });

        app('Dingo\Api\Exception\Handler')->register(function (JWTException $e) {
            $message = 'Server error';
            $error = new Error('unable_to_authenticate');
            $statusCode = 500;

            // TODO: Log error issue

            return response()->apiJsonError($message, $error, $statusCode);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
