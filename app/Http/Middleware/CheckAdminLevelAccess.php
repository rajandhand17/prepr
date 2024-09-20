<?php

namespace App\Http\Middleware;

use App\Helpers\UtilityHelper;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class CheckAdminLevelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::user()->hasRole('super_admin')) {
                return $next($request);
            }

            return Response::json(ResponseUtil::makeError(__('responses.admin_level_permission_error')), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
