<?php

namespace App\Http\Controllers\Maestro\Lab;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\SocialLinkService;
use App\Traits\Maestro\Lab\LabTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class LabController extends Controller
{
    use LabTrait;

    public function __construct()
    {
        $this->middleware('web');
        View::share('lab_privacy', ['public' => 'Public', 'private' => 'Private']);
    }

    public function getLabsBasedOnOrganization(Request $request)
    {
        try {
            $getList = $this->getLabsBasedOnOrganizations($request);
            if ($getList) {
                return $getList;
            }

            return response()->json(['status' => 'fail', 'message' => 'Sorry, There are no labs related to this organization', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }

    public function index(Builder $builder)
    {
        try {
            if (request()->ajax()) {
                $i = 1;
                $labes = Lab::orderBy('id', 'desc');

                return DataTables::eloquent($labes)
                ->addIndexColumn()
                    ->addColumn('action', static function (Lab $lab) {
                        $html = '';
                        $html .= '<a href="" class="mr-25 showUser" data-id="'.$lab->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="'.route('lab.edit', ['lab' => $lab->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$lab->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="javascript:void(0)" onclick="deleteLab(\''.route('lab.destroy', ['lab' => $lab->id]).'\')"> <i class="fas fa-trash"></i></a>';
                        if ($lab->is_pre_built == '0') {
                            $html .= '<a href="javascript:void(0)" onclick="ChallengeToLabTemplate(\''.route('lab-template.clone', ['slug' =>$lab->slug ]).'\')"> <i class="fas fa-clone"></i></a>';
                        }

                        return $html;
                    })
                    ->editColumn('user_id', static function (Lab $lab) {
                        if ($lab->user_id === 0 || $lab->user_id === '') {
                            return 'Admin';
                        } else {
                            return $lab->user->username ?? '';
                        }
                    })
                    ->editColumn('category', static function (Lab $lab) {
                        if ($lab->category_id === 0 || $lab->category_id === '') {
                            return '';
                        } else {
                            return $lab->getCategory->title ?? '';
                        }
                    })
                    ->toJson();
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => '', 'title' => 'Id', 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Lab Title'],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User Name'],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'width' => '10%', 'orderable' => false, 'searchable' => false],
            ])->parameters(['order' => [0, 'desc']]);
            View::share('module_name', 'Lab');

            return view('maestro.lab.index', compact('html'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('lab.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getLanguages();
            $social_name = SocialLinkService::getSocialLinkList();

            return view('maestro.lab.create', compact('languages', 'social_name'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('lab.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // checks creation limits of the Lab
            $checkLabLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($request->organization_id, 'lab');
            if ($checkLabLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkLabCount = $this->getLabCountBasedOnOrganization($checkLabLimit['organizationId']);
                if ($checkLabLimit['fetchOrganizationPlanDetails'] <= $checkLabCount) {
                    return redirect()->route('lab.index')->with(['error' => 'Lab limit reached']);
                }
            }
            if ($this->createLab($request)) {
                return redirect()->route('lab.index')->with('success', 'Lab created successfully');
            }

            return redirect()->route('lab.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('lab.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return view('maestro.lab.view', compact('lab'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('lab.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = Lab::find($id);
            $labAssociatedItems = $this->getLabAssociatedItemsById($data);
            $labSocialLink = $this->getLabExternalLinks($data->id);
            $social_name = SocialLinkService::getSocialLinkList();
            $languages = LanguageService::getLanguages();

            return view('maestro.lab.edit', compact('data', 'labSocialLink', 'languages', 'labAssociatedItems', 'social_name'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateLabById($id, $request)) {
                return redirect()->route('lab.index')->with('success', 'Lab Updated successfully');
            }

            return redirect()->route('lab.index')->withErrors(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('lab.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteLabById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
