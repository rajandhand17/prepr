<?php

namespace App\Http\Middleware;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class CheckTokenValidation
{
    /**
     * Handle an incoming request.
     *
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = Auth::user();

            if ($user) {
                // Get the latest token of the user
                $token = $user->token();

                // Check if token has expired
                if ($token && $token->expires_at->lt(Carbon::now())) {
                    // Token is expired
                    return Response::json(ResponseUtil::makeError(__('responses.token_expired')), 403);
                }
            }

            return $next($request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
