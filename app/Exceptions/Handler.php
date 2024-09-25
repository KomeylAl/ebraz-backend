<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $e) {
        if ($e instanceof UnauthorizedHttpException) {
            $previousException = $e->getPrevious();

            if ($previousException instanceof TokenExpiredException) {
                return response()->json([
                    'code' => -1,
                    'message' => 'توکن شما منقضی شده است. لطفاً دوباره وارد شوید.',
                    'data' => []
                ], 401);
            } else if ($previousException instanceof TokenInvalidException) {
                return response()->json([
                    'code' => -1,
                    'message' => 'توکن نامعتبر است. لطفاً دوباره وارد شوید.',
                    'data' => []
                ], 401);
            } else if ($previousException instanceof JWTException) {
                return response()->json([
                    'code' => -1,
                    'message' => 'خطایی در پردازش توکن رخ داده است.',
                    'data' => []
                ], 401);
            } else {
                return response()->json([
                    'code' => -1,
                    'message' => 'احراز هویت انجام نشد.',
                    'data' => []
                ], 401);
            }
        }

        return parent::render($request, $e);
    }
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
