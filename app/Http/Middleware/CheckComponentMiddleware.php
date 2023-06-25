<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class CheckComponentMiddleware
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
            $components = [
                'organization',
                'lab',
                'challenge',
                'project',
            ];
            if (in_array(request()->route()->parameter('component'), $components)) {
                return $next($request);
            }

            return Response::json(ResponseUtil::makeError('Please provide the valid component.'), 404);
        } catch (\Exception $e) {
            dd($e);

            return Response::json(ResponseUtil::makeError('Something went wrong getting the component.'), 500);
        }
    }
}
