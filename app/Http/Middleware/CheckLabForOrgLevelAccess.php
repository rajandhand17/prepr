<?php

namespace App\Http\Middleware;

use App\Helpers\UtilityHelper;
use Closure;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class CheckLabForOrgLevelAccess
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
            if (auth()->user()->isAbleTo(['view_lab', 'create_lab', 'edit_lab', 'delete_lab', 'view_lab_programs', 'create_lab_programs', 'edit_lab_programs', 'delete_lab_programs', 'view_lab_member', 'create_lab_member', 'edit_lab_member', 'delete_lab_member'], (int) auth()->user()->preferred_organization, true)) {
                return $next($request);
            }

            return Response::json(ResponseUtil::makeError(__('responses.org_level_permission_error')), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
