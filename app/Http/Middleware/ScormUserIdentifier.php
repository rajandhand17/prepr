<?php

namespace App\Http\Middleware;

use App\Services\Scorm\ScormUserTokenService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScormUserIdentifier
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tracking_id = $request->get('tracking_id');
        if (!$tracking_id) {
            return $this->handleMiddlewareVerificationFail($request);
        }

        $scormUserTokenService = $this->getScormServiceInstance();
        if ($scormUserTokenService) {
            /** USER WHO IS PLAYING THE SCORM */
            $scormUser = $scormUserTokenService->getTokenUser($tracking_id);
            if ($scormUser) {
                /** PASSING THE USER IN THE REQUEST */
                $request->merge(['scormUser' => $scormUser]);

                return $next($request);
            }
        }

        return $this->handleMiddlewareVerificationFail($request);
    }

    public function getScormServiceInstance(): ScormUserTokenService|false
    {
        try {
            /* @var ScormUserTokenService $scormService */
            $scormService = app()->make(ScormUserTokenService::class);

            return $scormService;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param Request $request
     *
     * @return View|JsonResponse
     */
    public function handleMiddlewareVerificationFail(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => __('Unauthorized.')], 401);
        }

        return view('404');
    }
}
