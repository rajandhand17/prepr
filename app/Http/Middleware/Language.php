<?php

namespace App\Http\Middleware;

use App\Helpers\UtilityHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class Language
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
            if (isset($request->language) && !empty($request->language)) {
                $check_language = \App\Models\Language::where('iso', $request->language)->first();

                if ($check_language) {
                    App::setlocale($request->language);

                    return $next($request);
                }

                return Response::json(ResponseUtil::makeError(__('responses.not_supported_language')), 400);
            }

            return Response::json(ResponseUtil::makeError(__('responses.provide_language')), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return Response::json(ResponseUtil::makeError(__('responses.something_wrong_setup_language')), 500);
        }
    }
}
