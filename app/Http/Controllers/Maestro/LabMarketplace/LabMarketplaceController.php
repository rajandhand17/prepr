<?php

namespace App\Http\Controllers\Maestro\LabMarketplace;

use App\Http\Controllers\Controller;
use App\Models\LabMarketplace;
use App\Models\Setting;
use App\Traits\Maestro\LabMarketplace\LabMarketplaceTrait;
use App\Traits\Maestro\Organization\OrganizationTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class LabMarketplaceController extends Controller
{
    use LabMarketplaceTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $labMarketplaceInfo = $this->getLabMarketplace();
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
                        })
                        ->addColumn('action', static function (LabMarketplace $labMarketplaceInfo) {
                            return '<a style="padding-left:50px" class="mr-10" href="#"><i class="fas fa-edit"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['privacy', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Title', "width" => "5%"],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', "width" => "10%"],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.labMarketplace.index', compact('html'));
        }catch (\Exception $e) {
            return false;
        }
    }
}
