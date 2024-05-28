<?php

namespace App\Http\Controllers\Maestro\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\Maestro\Users\MaestroUsersHelper;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use Exception;

class MaestroUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }
    public function index(Builder $builder, Request $request)
    {
        try {
            $usersInfo = MaestroUsersHelper::getUserForTableBuilder();
            if (!empty($usersInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($usersInfo)
                        ->editColumn('full_name', static function (User $user) {
                            return $user->first_name . ' ' . $user->last_name;
                        })
                        ->editColumn('status', static function (User $user) {
                            if ($user->status === 'active') {
                                $html = "<span class='badge badge-success'>Success</span>";
                            } else {
                                $html = "<span class='badge badge-danger' >Deactive</span>";
                            }
                            return $html;
                        })
                        ->addColumn('action', static function (User $user) {
                            return '<a style="padding-left:50px" class="mr-10" href="' . route('users.show', ['user' => $user->id]) . '"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="' . route('users.edit', ['user' => $user->id]) . '"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteUser(\'' . route('users.destroy', ['user' => $user->id]) . '\')"><i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['status', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
                ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Name', "width" => "35%"],
                ['data' => 'email', 'name' => 'email', 'title' => 'Email', "width" => "35%"],
                ['data' => 'status', 'name' => 'status', 'title' => 'status', "width" => "10%"],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', "width" => "15%", 'orderable' => false, 'searchable' => false],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.users.users-list', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('maestro.users.users-create');
        } catch (Exception $e) {
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (MaestroUsersHelper::createUser($request)) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User created successfully');
            }
            DB::rollback();
            return redirect()->route('users.index')->withErrors(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = MaestroUsersHelper::getUserById($id);
            return view('maestro.users.users-view', compact('user'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = MaestroUsersHelper::getUserById($id);
            return view('maestro.users.users-edit', compact('user'));
        } catch (Exception $e) {
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $userInfo = MaestroUsersHelper::getUserById($id);
            if (MaestroUsersHelper::updateUser($userInfo, $request)) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User Updated successfully');
            }
            DB::rollback();
            return redirect()->route('users.index')->withErrors(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $user = MaestroUsersHelper::getUserById($id);
            if ($user->delete()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
}
