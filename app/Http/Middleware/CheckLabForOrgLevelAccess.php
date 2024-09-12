<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Manage\OrganizationService;
use InfyOm\Generator\Utils\ResponseUtil;
use App\Helpers\UtilityHelper;
use Response;

class CheckLabForOrgLevelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request                                                                          $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if(auth()->user()->isAbleTo(['view_lab','create_lab','edit_lab','delete_lab'], (int) auth()->user()->preferred_organization, true)) {
                return $next($request);
            }
            return Response::json(ResponseUtil::makeError(__('responses.org_level_permission_error')), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
