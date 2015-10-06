<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Responses\Error;
use App\Exceptions\AbstractException;
use App\Exceptions\ExceedingIndexException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        echo 'abcdefg123';

        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            $message = 'Token expired';
            $error = new Error('token_expired');
            $statusCode = $e->getStatusCode();

            return response()->apiJsonError($message, $error, $statusCode);
        } elseif ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            $message = 'Token invalid';
            $error = new Error('token_invalid');
            $statusCode = $e->getStatusCode();

            return response()->apiJsonError($message, $error, $statusCode);
        } elseif ($e instanceof ExceedingIndexException) {
            echo 'qqq123';

            return response()->apiJsonError(
                    $e->getMessage(),
                    $e->getErrors(),
                    $e->statusCode());
        } elseif ($e instanceof AbstractException) {
            echo 'qqq456';

            return response()->apiJsonError(
                    $e->getMessage(),
                    $e->getErrors(),
                    $e->statusCode());
        } elseif ($e instanceof Exception) {
            echo 'qqq000';

            return response()->apiJsonError(
                    'jim',
                    '000',
                    $e->statusCode());        }

        return parent::render($request, $e);
    }
}
