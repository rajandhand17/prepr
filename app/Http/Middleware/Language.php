<?php

namespace App\Http\Middleware;

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
                return Response::json(ResponseUtil::makeError('Sorry! we are not supporting the language which you have requested for!.'), 400);
            }

            return Response::json(ResponseUtil::makeError('Please provide the language.'), 400);
        } catch (\Exception $e) {
            return Response::json(ResponseUtil::makeError('Something went wrong during setting up the application language.'), 500);
        }
    }
}
