<?php

namespace App\Http\Controllers\Maestro\Challenge;

use App\Http\Controllers\Controller;
use App\Models\LabMarketplace;
use App\Services\Maestro\Category\CategoryService;
use App\Services\Maestro\User\UserService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ChallengeController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $labMarketplaceInfo = $this->getChallenge();
            if (!empty($labMarketplaceInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($labMarketplaceInfo)
                        ->editColumn('privacy', static function (LabMarketplace $labMarketplaceInfo) {
                            switch ($labMarketplaceInfo->privacy){
                                case '0';
                                    $html = "<span class='badge badge-success'>public</span>";
                                    break;
                                case '1';
                                    $html = "<span class='badge badge-success'>private</span>";
                                    break;
                            }
                            return $html;
                        })->editColumn('category', static function (LabMarketplace $labMarketplaceInfo) {
                            $categoryId=CategoryService::getCategoryById($labMarketplaceInfo->category_id);
                            return $categoryId->title;
                        })->editColumn('username', static function (LabMarketplace $labMarketplaceInfo) {
                            $user=UserService::getUserById($labMarketplaceInfo->user_id);
                            return $user->username;
                        })
                        ->addColumn('action', static function (LabMarketplace $labMarketplaceInfo) {
                            return '<a href="javascript:void(0)" onclick="deleteLabMarketplace(\'' . route('lab-marketplace.destroy', ['lab_marketplace' => $labMarketplaceInfo->id]) . '\')"> <i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['privacy','category','username','action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Title', "width" => "5%"],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', "width" => "10%"],
                ['data' => 'username', 'name' => 'username', 'title' => 'Username', "width" => "10%"],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category', "width" => "10%"],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', "width" => "10%"],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.labMarketplace.index', compact('html'));
        }catch (\Exception $e) {
            return false;
        }
    }
}
