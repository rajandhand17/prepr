<?php

namespace App\Http\Controllers\Maestro\Resources;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\ResourceModule;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\OrganizationService;
use App\Services\Maestro\UserService;
use App\Traits\Maestro\Resource\ResourceModuleTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ResourceModuleController extends Controller
{
    use ResourceModuleTrait;

    public function index(Builder $builder, Request $request)
    {
        try {
            $resourceModule = $this->getResourceModuleList();
            if (request()->ajax()) {
                return DataTables::eloquent($resourceModule)
                    ->addIndexColumn()
                    ->editColumn('title', static function (ResourceModule $resourceModule) {
                        return $resourceModule->title;
                    })
                    ->editColumn('status', static function (ResourceModule $resourceModule) {
                        if ($resourceModule->status == '0') {
                            $html = "<span class='badge badge-info'>Draft</span>";
                        } elseif ($resourceModule->status == '1') {
                            $html = "<span class='badge badge-success'>Published</span>";
                        } elseif ($resourceModule->status == '2') {
                            $html = "<span class='badge badge-danger'>Archive</span>";
                        }

                        return $html;
                    })
                    ->editColumn('privacy', static function (ResourceModule $resourceModule) {
                        if ($resourceModule->privacy == '0') {
                            $html = "<span class='badge badge-info'>Public</span>";
                        } else {
                            $html = "<span class='badge badge-success'>Private</span>";
                        }

                        return $html;
                    })
                    ->editColumn('is_global', static function (ResourceModule $resourceModule) {
                        if ($resourceModule->is_global == '0') {
                            $html = "<span class='badge badge-info'>No</span>";
                        } else {
                            $html = "<span class='badge badge-success'>Yes</span>";
                        }

                        return $html;
                    })
                    ->editColumn('media', static function (ResourceModule $resourceModule) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='".$resourceModule->media."' width='30px' ".$onerror.'>';
                    })
                    ->addIndexColumn()
                    ->addColumn('action', static function (ResourceModule $resourceModule) {
                        return '<a class="mr-10" href="'.route('resource-module.edit', ['resource_module' => $resourceModule->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteResourceModule(\''.route('resource-module.destroy', ['resource_module' => $resourceModule->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['media', 'privacy', 'is_global', 'action', 'DT_Row_Index'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Resource Name', 'width' => '45%'],
                ['data' => 'media', 'name' => 'media', 'title' => 'Cover Image', 'width' => '10%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', 'width' => '10%'],
                ['data' => 'is_global', 'name' => 'is_global', 'title' => 'Globally Available?', 'width' => '10%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '8%'],
            ])->parameters([
                'order' => [[1, 'asc']],
            ]);

            return view('maestro.resource-module.index', compact('html'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getLanguages();

            return view('maestro.resource-module.create', compact('languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            return view('maestro.resource-module.show');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createAndUpdateResourceModule($request, 'create', null)) {
                return redirect()->route('resource-module.index')->with('success', 'Resource Module created successfully');
            }

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $resourceModule = $this->getResourceModuleById($id);
            if (!$resourceModule->exists) {
                return redirect()->route('resource-module.index')->with(['error' => 'Resource Module not found.']);
            }
            $languages = LanguageService::getLanguages();
            $user = UserService::getUser('edit', $resourceModule->user_id);
            $organization = OrganizationService::getOrganization($resourceModule->organization_id);

            return view('maestro.resource-module.edit', compact('languages', 'resourceModule', 'user', 'organization'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->createAndUpdateResourceModule($request, 'update', $id)) {
                return redirect()->route('resource-module.index')->with('success', 'Resource Module Updated successfully');
            }

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteResourceModuleById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Resource Module deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
