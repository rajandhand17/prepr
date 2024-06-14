<?php

namespace App\Http\Middleware;

use App\Models\ChannelApis;
use App\Models\ChannelVendor;
use App\Models\ChannelVendorApiAccess;
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

        $vendor = ChannelVendor::where(['api_key' => $request->header('api-key'), 'secret_key' => $request->header('secret')])->first();
        if (!$vendor) {
            return Response::json(ResponseUtil::makeError(__('responses.vendor_not_found')), 404);
        }

        $channelApi = ChannelApis::where(['api_slug' => $request->route()->getName(), 'is_active' => 1])->first();
        if (!$channelApi) {
            return Response::json(ResponseUtil::makeError(__('responses.route_not_found')), 404);
        }

        $hasApiAccess = ChannelVendorApiAccess::where(['channel_vendor_id' => $vendor->id, 'channel_api_id' => $channelApi->id])->first();
        if (!$hasApiAccess) {
            return Response::json(ResponseUtil::makeError(__('responses.no_access_to_api')), 401);
        }

        return $next($request);
    }
}
