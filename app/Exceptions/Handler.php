<?php

namespace App\Exceptions;

use App\Helpers\UtilityHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InfyOm\Generator\Utils\ResponseUtil;
use League\Container\Exception\NotFoundException;
use Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

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
        dd($e->getMessage(),$e->getLine());
        if ($this->shouldReport($e)) {
            UtilityHelper::logError($e);
        }
        if ($e instanceof NotFoundException) {
            return Response::json(ResponseUtil::makeError(__('responses.handler_not_found_404')), 404);
        }
        if ($e instanceof AuthenticationException) {
            return Response::json(ResponseUtil::makeError(__('responses.handler_access_denied_403')), 403);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return Response::json(ResponseUtil::makeError(__('responses.handler_illegal_request_403')), 405);
        }

        if ($e) {
            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }

        return parent::render($request, $e);
    }
}
