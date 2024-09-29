<?php

namespace App\Http\Controllers\Maestro\Users;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Maestro\RoleAndPermissionService;
use App\Traits\Maestro\User\UserTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class UsersController extends Controller
{
    use UserTrait;

    public function index(Builder $builder, Request $request)
    {
        try {
            $usersInfo = $this->getUsers();
            $roles = RoleAndPermissionService::getAllRoles();
            if (!empty($usersInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($usersInfo)
                        ->editColumn('full_name', static function (User $usersInfo) {
                            return $usersInfo->first_name.' '.$usersInfo->last_name;
                        })
                        ->editColumn('is_deactivated', static function (User $usersInfo) {
                            if ($usersInfo->is_deactivated == '0') {
                                $html = "<span class='badge badge-success'>Active</span>";
                            } else {
                                $html = "<span class='badge badge-danger' >Inactive</span>";
                            }

                            return $html;
                        })
                        ->addColumn('roles', static function (User $usersInfo) use ($roles) {
                            $roleNames = [];
                            if (!empty($roles)) {
                                foreach ($roles as $key => $role) {
                                    if (in_array($role->name, $usersInfo->getRoles())) {
                                        $roleName = $role->display_name;
                                        $roleNames[] = $roleName;
                                    }
                                }
                            }

                            return $roleNames ? implode(',', $roleNames) : 'user';
                        })

                        ->addColumn('action', static function (User $usersInfo) {
                            return '<a style="padding-left:10px" class="mr-10" href="'.route('users.show', ['user' => $usersInfo->id]).'"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="'.route('users.edit', ['user' => $usersInfo->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteUser(\''.route('users.destroy', ['user' => $usersInfo->id]).'\')"><i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['is_deactivated', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Name', 'width' => '25%'],
                ['data' => 'roles', 'name' => 'roles', 'title' => 'Roles', 'width' => '25%'],
                ['data' => 'email', 'name' => 'email', 'title' => 'Email', 'width' => '25%'],
                ['data' => 'is_deactivated', 'name' => 'is_deactivated', 'title' => 'status', 'width' => '5%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'width' => '15%', 'orderable' => false, 'searchable' => false],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.users.index', compact('html'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = RoleAndPermissionService::getAllRoles();
            $permissions = RoleAndPermissionService::permissions();

            return view('maestro.users.create', compact('roles', 'permissions'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createUser($request)) {
                return redirect()->route('users.index')->with('success', 'User created successfully');
            }

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->getUserById($id);
            if (!$user->exists) {
                return redirect()->route('users.index')->with(['error' => 'User not found.']);
            }

            return view('maestro.users.view', compact('user'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = $this->getUserById($id);
            if (!$user->exists) {
                return redirect()->route('users.index')->with(['error' => 'User not found.']);
            }
            $roles = RoleAndPermissionService::getAllRoles();
            $permissions = RoleAndPermissionService::permissions();

            return view('maestro.users.edit', compact('user', 'permissions', 'roles'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateUserById($id, $request)) {
                return redirect()->route('users.index')->with('success', 'User Updated successfully');
            }

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('users.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteUserById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
