<?php

namespace App\Http\Middleware;

use App\Services\Manage\ChannelApiService;
use App\Services\Manage\ChannelVendorApiAccessService;
use App\Services\Manage\ChannelVendorService;
use Closure;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class ChannelApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('secret')) {
            return Response::json(ResponseUtil::makeError(__('responses.secret_key_required')), 401);
        }

        if (!$request->hasHeader('api-key')) {
            return Response::json(ResponseUtil::makeError(__('responses.api_key_required')), 401);
        }

        $vendor = ChannelVendorService::findVendorByApiKeyAndSecret($request->header('api-key'), $request->header('secret'));
        if (!$vendor) {
            return Response::json(ResponseUtil::makeError(__('responses.vendor_not_found')), 404);
        }

        $channelApi = ChannelApiService::getChannelApiByName($request->route()->getName());
        if (!$channelApi) {
            return Response::json(ResponseUtil::makeError(__('responses.route_not_found')), 404);
        }

        $hasApiAccess = ChannelVendorApiAccessService::hasApiAccess($vendor->id, $channelApi->id);

        if (!$hasApiAccess) {
            return Response::json(ResponseUtil::makeError(__('responses.no_access_to_api')), 401);
        }

        return $next($request);
    }
}
