<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

/**
 * Error response macro
 * What is Response Macros: http://laravel.com/docs/5.1/responses#response-macros
 */
class ErrorResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        /**
         * Factory an response()->apiJsonError() for error response 
         * 
         * @param   String  $message       response message
         * @param   Array   $errors   errors content
         * @param   int     $statusCode    HTTP response code
         * @return  Response
         */
        $factory->macro('apiJsonError', function ($message, $errors, $statusCode) use ($factory) {
            $jsonResponse = [
                'message' => $message,
                'errors' => [ $errors ]
            ];

            return response()->make($jsonResponse, $statusCode);
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
