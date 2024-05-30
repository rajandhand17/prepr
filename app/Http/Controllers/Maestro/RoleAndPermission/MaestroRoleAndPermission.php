<?php

namespace App\Http\Controllers\Maestro\RoleAndPermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\Maestro\Users\MaestroUsersHelper;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;
use Exception;

class MaestroRoleAndPermission extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder, Request $request)
    {
        try {
            $roles = Role::query();
            if (request()->ajax()) {
                return DataTables::eloquent($roles)
                    ->editColumn('display_name', function (Role $role) {
                        if (!empty ($role->display_name)) {
                            return $role->display_name;
                        } else {
                            return $role->name;
                        }
                    })
                    ->addColumn('action', function (Role $role) {
                        if (isset ($role->id)) {
                            return '<a class="mr-10" href="' . route('role.edit', ['role' => $role->id]) . '"><i class="fas fa-edit"></i></a>';
                        }
                    })

                    ->setRowData([
                        'data-id' => static function ($role) {
                            return 'row-' . $role->id;
                        },
                        'data-name' => static function ($role) {
                            return 'row-' . $role->name;
                        },
                    ])
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->toJson();
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => '#', "orderable" => false, "searchable" => false, "width" => '20%'],
                ['data' => 'display_name', 'name' => 'display_name', 'title' => 'Name', 'orderable' => false, "width" => '75%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, "width" => '5%'],
            ])->parameters(["pageLength" => 10,]);
            return view('maestro.roleandpermission.role-and-permission-list', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $permissions = Permission::orderBy('id', 'asc')->get();
            return view('maestro.roleandpermission.role-and-permission-create', compact('permissions'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $roleName = strtolower(str_replace(' ', '_', trim($request->display_name)));
            $role = Role::create(['name' => $roleName,'display_name' => trim($request->display_name),'description' => trim($request->description)]);
            $role->syncPermissions(!empty($request->permission) ? $request->permission : []);
            return redirect()->route('role.index')->with('success', 'Role Created Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $permissions = Permission::orderBy('id', 'asc')->get();
            $role = Role::find($id);
            $role_permission = $role->permissions()->pluck('id')->toArray();
            return view('maestro.roleandpermission.role-and-permission-edit', compact('role', 'role_permission','permissions'));
        } catch (Exception $e) {
            return redirect()->route('role.index')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $role = Role::find($id);
            $role->name = $request->name;
            $role->save();
            $role->syncPermissions(!empty($request->permission) ? $request->permission : []);
            
            return redirect()->route('role.index')->with('success', 'Data Updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('role.index')->with(['error' => $e->getMessage()]);
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
