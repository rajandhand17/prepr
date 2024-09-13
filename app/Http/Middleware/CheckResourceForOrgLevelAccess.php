<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Manage\OrganizationService;
use InfyOm\Generator\Utils\ResponseUtil;
use App\Helpers\UtilityHelper;
use Response;

class CheckResourceForOrgLevelAccess
{
    /**
     * Handle an incoming request.
     *
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if(auth()->user()->isAbleTo(['create_resource_module','view_resource_module','edit_resource_module','delete_resource_module','view_resource_group','create_resource_group','delete_resource_group','edit_resource_group','view_resource_collection','create_resource_collection','edit_resource_collection','delete_resource_collection'], (int) auth()->user()->preferred_organization, true)) {
                return $next($request);
            }
            return Response::json(ResponseUtil::makeError(__('responses.org_level_permission_error')), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
