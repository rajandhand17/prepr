<?php

namespace App\Http\Controllers\Maestro\Master;

use App\Http\Controllers\Controller;
use App\Traits\Maestro\Master\MasterTrait;
use Exception;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    use MasterTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * Display a listing of the dashboard components.
     */
    public function getOrganizations(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getOrganizationsById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getCategories(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getCategoriesById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getSkills(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getSkillsById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $response = $this->getUsersById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getLabs(Request $request)
    {
        try {
            if (!$request->language) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            } elseif (!$request->org_id) {
                return response()->json(['status' => 'fail', 'message' => 'Please select organization first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getLabsById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getResourceModules(Request $request)
    {
        try {
            if (!$request->language) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            } elseif (!$request->org_id) {
                return response()->json(['status' => 'fail', 'message' => 'Please select organization first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getResourceModulesById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getLevels(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getLevelsById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getDurations(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getDurationsById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function getMinRanks(Request $request)
    {
        try {
            if (!$request->language || is_null($request->language)) {
                return response()->json(['status' => 'fail', 'message' => 'Please select language first.', 'result' => [], 'more' => false, 'total_count' => 0]);
            }
            $response = $this->getMinRanksById($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function switchLanguage(Request $request)
    {
        try {
            $language = $request->language ? $request->language : 'en';
            \Session::put('globalLocale', $language);

            return response()->json(['status' => 'success', 'message' => 'Language switched successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    public function getUserEmail(Request $request)
    {
        try {
            $response = $this->getUsersEmail($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }
}
