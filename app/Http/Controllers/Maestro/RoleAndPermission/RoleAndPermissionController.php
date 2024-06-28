<?php

namespace App\Http\Controllers\Maestro\RoleAndPermission;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Traits\Maestro\RoleAndPermission\RoleAndPermissionTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class RoleAndPermissionController extends Controller
{
    use RoleAndPermissionTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder, Request $request)
    {
        try {
            $roles = $this->getRoles();
            if (request()->ajax()) {
                return DataTables::eloquent($roles)
                    ->editColumn('display_name', function (Role $role) {
                        if (!empty($role->display_name)) {
                            return $role->display_name;
                        } else {
                            return $role->name;
                        }
                    })
                    ->addColumn('action', function (Role $role) {
                        if (isset($role->id)) {
                            return '<a class="mr-10" href="'.route('role.edit', ['role' => $role->id]).'"><i class="fas fa-edit"></i></a>';
                        }
                    })

                    ->setRowData([
                        'data-id' => static function ($role) {
                            return 'row-'.$role->id;
                        },
                        'data-name' => static function ($role) {
                            return 'row-'.$role->name;
                        },
                    ])
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->toJson();
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => '#', 'orderable' => false, 'searchable' => false, 'width' => '20%'],
                ['data' => 'display_name', 'name' => 'display_name', 'title' => 'Name', 'orderable' => false, 'width' => '75%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ])->parameters(['pageLength' => 10]);

            return view('maestro.roleandpermission.role-and-permission-list', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->withErrors(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $permissions = $this->getPermissions();

            return view('maestro.roleandpermission.role-and-permission-create', compact('permissions'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createRole($request)) {
                DB::commit();

                return redirect()->route('role.index')->with('success', 'Role Created Successfully');
            }
            DB::rollback();

            return redirect()->route('role.index')->with('error', 'Something Want Wrong.');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $role = $this->getRoleById($id);
            $permissions = $this->getPermissions();
            $role_permission = $this->getPermissionBYRoleId($id);

            return view('maestro.roleandpermission.role-and-permission-edit', compact('role', 'role_permission', 'permissions'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateRole($id, $request)) {
                DB::commit();

                return redirect()->route('role.index')->with('success', 'Data Updated successfully.');
            }
            DB::rollback();

            return redirect()->route('role.index')->with('error', 'Something Want Wrong.');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('role.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
