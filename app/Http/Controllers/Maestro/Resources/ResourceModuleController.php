<?php

namespace App\Http\Controllers\Maestro\Resources;

use App\Http\Controllers\Controller;
use App\Models\ResourceModule;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Resource\ResourceModuleTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ResourceModuleController extends Controller
{
    use ResourceModuleTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

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
                            $html = 'Draft';
                        } elseif ($resourceModule->status == '1') {
                            $html = 'Published';
                        } elseif ($resourceModule->status == '2') {
                            $html = 'Archive';
                        }

                        return $html;
                    })
                    ->editColumn('privacy', static function (ResourceModule $resourceModule) {
                        if ($resourceModule->privacy == '0') {
                            $html = 'Not available globally';
                        } else {
                            $html = 'Available globally';
                        }

                        return $html;
                    })
                    ->addColumn('action', static function (ResourceModule $resourceModule) {
                        return '<a class="mr-10" href="'.route('resource-module.edit', ['resource_module' => $resourceModule->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteResourceModule(\''.route('resource-module.destroy', ['resource_module' => $resourceModule->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['icon', 'action', 'DT_Row_Index'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Resource Name', 'width' => '65%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', 'width' => '15%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
            ])->parameters([
                'order' => [[1, 'asc']],
            ]);

            return view('maestro.resourcemodule.index', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $status = $this->getResourceModuleStatus();
            $users = $this->getResourceModuleUser();
            $privacy = $this->getResourceModulePrivacy();
            $organizations = $this->getResourceModuleOrganization();

            return view('maestro.resourcemodule.create', compact('users', 'languages', 'status', 'privacy', 'organizations'));
        } catch (Exception $e) {
            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            return view('maestro.resourcemodule.show');
        } catch (Exception $e) {
            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createResourceModule($request)) {
                DB::commit();

                return redirect()->route('resource-module.index')->with('success', 'Resource Module created successfully');
            }
            DB::rollback();

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

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
            $languages = LanguageService::getAllActiveLanguages();
            $status = $this->getResourceModuleStatus();
            $users = $this->getResourceModuleUser();
            $privacy = $this->getResourceModulePrivacy();
            $organizations = $this->getResourceModuleOrganization();

            return view('maestro.resourcemodule.edit', compact('users', 'languages', 'status', 'privacy', 'resourceModule', 'organizations'));
        } catch (Exception $e) {
            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateResourceModuleById($id, $request)) {
                DB::commit();

                return redirect()->route('resource-module.index')->with('success', 'Resource Module Updated successfully');
            }
            DB::rollback();

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('resource-module.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteResourceModuleById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Resource Module deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
