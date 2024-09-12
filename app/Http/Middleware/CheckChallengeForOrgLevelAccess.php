<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Manage\OrganizationService;
use InfyOm\Generator\Utils\ResponseUtil;
use App\Helpers\UtilityHelper;
use Response;

class CheckChallengeForOrgLevelAccess
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
            if(auth()->user()->isAbleTo(['view_challenge','create_challenge','edit_challenge','delete_challenge','select_challenge_winner','view_challenge_assessment','update_challenge_assessment','clone_challenge','create_challenge_annoucements','delete_challenge_annoucements','list_challenge_annoucements','view_challenges_path','create_challenges_path','edit_challenges_path','delete_challenges_path'], (int) auth()->user()->preferred_organization, true)) {
                return $next($request);
            }
            return Response::json(ResponseUtil::makeError(__('responses.org_level_permission_error')), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return Response::json(ResponseUtil::makeError(__('responses.send_error')), 500);
        }
    }
}
