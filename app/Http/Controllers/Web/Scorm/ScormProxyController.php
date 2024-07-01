<?php

namespace App\Http\Controllers\Web\Scorm;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Public\Scorm\ScormRepository;
use Symfony\Component\HttpFoundation\Response;

class ScormProxyController extends Controller
{
    public function __construct(
        protected ScormRepository $scormRepository,
    ) {
    }

    public function scormFileLink(string $url)
    {
        try {
            $proxy = $this->scormRepository->generateScormProxy($url);

            if ($proxy) {
                $url = data_get($proxy, 'url');
                if ($url) {
                    return redirect($url);
                }
                $response = response(data_get($proxy, 'binary'));
                $contentType = data_get($proxy, 'content_type');
                if ($contentType) {
                    $response->header('Content-Type', $contentType);
                }

                return $response;
            }

            return response('Something went wrong !', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return response('Something went wrong !', Response::HTTP_BAD_REQUEST);
        }
    }
}
