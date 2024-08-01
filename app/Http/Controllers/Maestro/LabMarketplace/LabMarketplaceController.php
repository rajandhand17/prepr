<?php

namespace App\Http\Controllers\Maestro\LabMarketplace;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manage\LabMarketplace\LabMarketplaceResource;
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
        $this->middleware('web');
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
                ['data' => 'title', 'name' => 'title', 'title' => 'Title', 'width' => '5%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', 'width' => '10%'],
                ['data' => 'username', 'name' => 'username', 'title' => 'Username', 'width' => '10%'],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category', 'width' => '10%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '10%'],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.labMarketplace.index', compact('html'));
        } catch (\Exception $e) {
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
                return response()->json(['status' => 'success', 'message' => 'Lab Marketplace deleted successfully']);
            }

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function clone($slug)
    {
        try {
            $checkLabExistsOrNot = $this->getLabBasedOnSlug($slug);
            if (!$checkLabExistsOrNot) {
                return $this->sendError(__('responses.lab_not_found'), 404);
            }

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            if ($checkLabExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.lab_switcher_error'), 403);
            }

            if ($checkLabExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.lab_not_accessible'), 403);
            }
            if($checkLabExistsOrNot->is_pre_built=='1'){
                return $this->sendError(__('responses.lab_already_cloned'), 422);
            }
            $labMarketplace = $this->addLabToMarketplace($slug, $checkLabExistsOrNot->id);
            if ($labMarketplace) {
                return $this->sendResponse(LabMarketplaceResource::make($labMarketplace), __('responses.lab_marketplace_stored_success'), 200);
            }

            return $this->sendError(__('responses.lab_marketplace_stored_failed'), 400);
        }catch (\Exception $e){
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
