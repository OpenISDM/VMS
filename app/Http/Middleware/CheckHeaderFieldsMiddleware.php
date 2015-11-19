<?php

namespace App\Http\Middleware;

use Closure;
use App\ApiKey;

class CheckHeaderFieldsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get API key header fields
        $apiKey = $request->header('X-VMS-API-Key');
        // Query the API key
        $apiKeyCount = ApiKey::where('api_key', '=', $apiKey)->count();

        if ($apiKeyCount != 1) {
            // API key is not found
            $message = 'API key is not validated';
            $errorArray = ['code' => 'incorrect_api_key'];
            $statusCode = 401;

            return response()->apiJsonError($message, $errorArray, $statusCode);
        }

        return $next($request);
    }
}
