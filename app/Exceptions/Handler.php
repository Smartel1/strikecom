<?php

namespace App\Exceptions;

use Exception;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Exception\UnknownKey;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     * @throws Exception
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof AuthorizationException) {
            return response($e->getMessage(), 403);
        }
        if ($e instanceof UnknownKey) {
            return response($e->getMessage(), 401);
        }
        if ($e instanceof InvalidToken) {
            return response($e->getMessage(), 401);
        }
        if ($e instanceof ValidationException) {
            return response($e->errors(), 422);
        }

        if ($e instanceof ModelNotFoundException) {
            return response('модель не найдена', 404);
        }

        return parent::render($request, $e);
    }
}
