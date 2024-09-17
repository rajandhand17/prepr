<?php

namespace App\Http\Middleware;

use App\Helpers\UtilityHelper;
use Closure;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class CheckOrganizationForOrgLevelAccess
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
            if (auth()->user()->isAbleTo(['view_organization', 'create_organization', 'edit_organization', 'delete_organization', 'view_organization_members', 'create_organization_members', 'edit_organization_members', 'delete_organization_members'], (int) auth()->user()->preferred_organization, true)) {
                return $next($request);
            }

            return Response::json(ResponseUtil::makeError(__('responses.org_level_permission_error')), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
