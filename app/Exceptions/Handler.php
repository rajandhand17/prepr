<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InfyOm\Generator\Utils\ResponseUtil;
use League\Container\Exception\NotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Response;

class Handler extends ExceptionHandler
{
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

        });
    }

    public function render($request, Throwable $e)
    {
        if($e instanceof NotFoundException){
            return Response::json(ResponseUtil::makeError('404! not found'), 404);
        }
        if($e instanceof AuthenticationException){
            return Response::json(ResponseUtil::makeError('403! Access Denied. Please login.'), 403);
        }

        if($e instanceof MethodNotAllowedHttpException){
            return Response::json(ResponseUtil::makeError('403! Illegal Request.'), 405);
        }


        return parent::render($request, $e);
    }
}
