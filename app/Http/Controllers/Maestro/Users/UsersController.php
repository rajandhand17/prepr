<?php

namespace App\Http\Controllers\Maestro\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Maestro\User\UserTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class UsersController extends Controller
{
    use UserTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $usersInfo = $this->getUsers();
            if (!empty($usersInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($usersInfo)
                        ->editColumn('full_name', static function (User $usersInfo) {
                            return $usersInfo->first_name.' '.$usersInfo->last_name;
                        })
                        ->editColumn('status', static function (User $usersInfo) {
                            if ($usersInfo->status === 'active') {
                                $html = "<span class='badge badge-success'>Success</span>";
                            } else {
                                $html = "<span class='badge badge-danger' >Deactive</span>";
                            }

                            return $html;
                        })
                        ->addColumn('action', static function (User $usersInfo) {
                            return '<a style="padding-left:50px" class="mr-10" href="'.route('users.show', ['user' => $usersInfo->id]).'"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="'.route('users.edit', ['user' => $usersInfo->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteUser(\''.route('users.destroy', ['user' => $usersInfo->id]).'\')"><i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['status', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Name', 'width' => '35%'],
                ['data' => 'email', 'name' => 'email', 'title' => 'Email', 'width' => '35%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'status', 'width' => '10%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'width' => '15%', 'orderable' => false, 'searchable' => false],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.users.index', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = $this->getAllRoles();

            return view('maestro.users.create', compact('roles'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createUser($request)) {
                DB::commit();

                return redirect()->route('users.index')->with('success', 'User created successfully');
            }
            DB::rollback();

            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
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
            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = $this->getUserById($id);
            if (!$user->exists) {
                return redirect()->route('users.index')->with(['error' => 'User not found.']);
            }
            $roles = $this->getAllRoles();
            $permissions = $this->getAllPermissions();
            $selected_role = $user->getRoles();
            $assigned_all_permission = $user->allPermissions()->pluck('id')->toArray();

            return view('maestro.users.edit', compact('user', 'permissions', 'assigned_all_permission', 'roles', 'selected_role'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateUserById($id, $request)) {
                DB::commit();

                return redirect()->route('users.index')->with('success', 'User Updated successfully');
            }
            DB::rollback();

            return redirect()->route('users.index')->with(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteUserById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something want wrong.']);
        }
    }
}
