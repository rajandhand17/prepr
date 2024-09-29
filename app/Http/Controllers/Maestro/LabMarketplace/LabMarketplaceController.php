<?php

namespace App\Http\Controllers\Maestro\LabMarketplace;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\LabMarketplace;
use App\Services\Maestro\CategoryService;
use App\Services\Maestro\LabMarketplaceService;
use App\Services\Maestro\UserService;
use App\Traits\Maestro\LabMarketplace\LabMarketplaceTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class LabMarketplaceController extends Controller
{
    use LabMarketplaceTrait;
    protected $labMarketplaceService;

    public function __construct(LabMarketplaceService $labMarketplaceService)
    {
        $this->labMarketplaceService = $labMarketplaceService;
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            // Getting Lab marketplace data
            $labMarketplaceInfo = $this->getLabMarketplace();
            if (!empty($labMarketplaceInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($labMarketplaceInfo)
                        ->editColumn('privacy', static function (LabMarketplace $labMarketplaceInfo) {
                            switch ($labMarketplaceInfo->privacy) {
                                case '0':
                                    $html = "<span class='badge badge-success'>public</span>";
                                    break;
                                case '1':
                                    $html = "<span class='badge badge-success'>private</span>";
                                    break;
                            }

                            return $html;
                        })->editColumn('category', static function (LabMarketplace $labMarketplaceInfo) {
                            $categoryId = CategoryService::getCategoryById($labMarketplaceInfo->category_id);

                            return $categoryId->title;
                        })->editColumn('username', static function (LabMarketplace $labMarketplaceInfo) {
                            $user = UserService::getUserById($labMarketplaceInfo->user_id);

                            return $user->username;
                        })
                        ->addColumn('action', static function (LabMarketplace $labMarketplaceInfo) {
                            return '<a href="javascript:void(0)" onclick="deleteLabMarketplace(\''.route('lab-marketplace.destroy', ['lab_marketplace' => $labMarketplaceInfo->id]).'\')"> <i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['privacy', 'category', 'username', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Title', 'width' => '20%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', 'width' => '5%'],
                ['data' => 'username', 'name' => 'username', 'title' => 'Username', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '2%'],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.lab-marketplace.index', compact('html'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            // Checking lab marketplace exists or not
            $checkLabMarketPlaceExistsOrNot = $this->getLabMarketplaceById($id);
            if (!$checkLabMarketPlaceExistsOrNot) {
                return response()->json(['status' => 'fail', 'message' => 'This LabMarketplace does not exist']);
            }
            // Deleting lab marketplace based on id
            if ($this->deleteLabMarketplaceById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Lab Template deleted successfully from Lab Marketplace ']);
            }

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function clone($slug)
    {
        try {
            $checkLabExistsOrNot = $this->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot) {
                return response()->json(['status' =>'fail', 'message'=>'This lab does not exist in the database.']);
            }

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return response()->json(['status' =>'fail', 'message'=>'This organization does not exist in the database.']);
            }

            if ($checkLabExistsOrNot->is_accessible == '0') {
                return response()->json(['status' =>'fail', 'message'=>'Sorry, this Lab is not accessible with your existing plan.']);
            }

            if ($checkLabExistsOrNot->is_pre_built == '1') {
                return response()->json(['status' =>'fail', 'message'=>'This Lab is already added in Lab Marketplace.']);
            }

            $labMarketplace = $this->addLabToMarketplace($slug, $checkLabExistsOrNot->id);
            if ($labMarketplace) {
                return response()->json(['status' =>'success', 'message'=>'This Lab has been successfully added in Lab Marketplace.']);
            }

            return response()->json(['status' =>'fail', 'message'=>'This Lab failed to adding in lab marketplace.']);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
