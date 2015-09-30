<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ErrorResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('apiJsonError', function ($message, $errorsArray, $statusCode) use ($factory) {
            $jsonResponse = [
                'message' => $message,
                'errors' => [ $errorsArray ]
            ];

            return $factory->json($jsonResponse, $statusCode);
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
