<?php

namespace App\Http\Middleware;

use App\Services\Manage\MemberManagementService;
use Closure;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class VerifyGO1Access
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $memberManagement = new MemberManagementService();
        if (!$memberManagement->canCreateGO1Resource()) {
            return Response::json(ResponseUtil::makeError(__('responses.go1_access_denied')), 400);
        }

        return $next($request);
    }
}
