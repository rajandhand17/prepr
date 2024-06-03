<?php

namespace App\Http\Controllers\Maestro\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Traits\Maestro\User\UserTrait;
use App\Models\Category;
use App\Models\Language;
use Exception;

class CategoryController extends Controller
{
    use UserTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    // public function index(Builder $builder, Request $request)
    // {
    //     try {
    //         $usersInfo = $this->getUsers();
    //         if (!empty($usersInfo)) {
    //             if ($request->ajax()) {
    //                 return DataTables::eloquent($usersInfo)
    //                     ->editColumn('full_name', static function (User $usersInfo) {
    //                         return $usersInfo->first_name . ' ' . $usersInfo->last_name;
    //                     })
    //                     ->editColumn('status', static function (User $usersInfo) {
    //                         if ($usersInfo->status === 'active') {
    //                             $html = "<span class='badge badge-success'>Success</span>";
    //                         } else {
    //                             $html = "<span class='badge badge-danger' >Deactive</span>";
    //                         }
    //                         return $html;
    //                     })
    //                     ->addColumn('action', static function (User $usersInfo) {
    //                         return '<a style="padding-left:50px" class="mr-10" href="' . route('users.show', ['user' => $usersInfo->id]) . '"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="' . route('users.edit', ['user' => $usersInfo->id]) . '"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteUser(\'' . route('users.destroy', ['user' => $usersInfo->id]) . '\')"><i class="fas fa-trash"></i></a>';
    //                     })
    //                     ->addIndexColumn()
    //                     ->rawColumns(['status', 'action'])
    //                     ->make(true);
    //             }
    //         }

    //         $html = $builder->columns([
    //             ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
    //             ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Name', "width" => "35%"],
    //             ['data' => 'email', 'name' => 'email', 'title' => 'Email', "width" => "35%"],
    //             ['data' => 'status', 'name' => 'status', 'title' => 'status', "width" => "10%"],
    //             ['data' => 'action', 'name' => 'Action', 'title' => 'Action', "width" => "15%", 'orderable' => false, 'searchable' => false],
    //         ])->parameters(['order' => [0, 'desc']]);

    //         return view('maestro.users.index', compact('html'));
    //     } catch (Exception $e) {
    //         return redirect()->route('users.index')->withErrors(['error' => $e->getMessage()]);
    //     }
    // }

    public function index(Builder $builder)
    {
        try {
            $categories = Category::where(['parent_id' => '0'])->orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::eloquent($categories)
                    ->addColumn('action', static function (Category $category) {
                        return '<a href="' . url('maestro/categories/' . $category->id . '/edit') . '" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="' . $category->id . '"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" class="mr-25 deleteCat" data-id="' . $category->id . '"><i class="fa fa-trash-o"></i></a>';
                    })
                    ->addColumn('child', static function (Category $category) {
                        return '<a href="' . url('maestro/sub/categories/' . $category->id) . '">View Category</a>';
                    })->rawColumns(['child', 'action'])
                    ->toJson();
            }

            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ];
            foreach ($languages as $single) {
                if ($single->lang_iso == 'en') {
                    $columName = 'title';
                } else {
                    $columName = $single->lang_iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName . '_title';
                }
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->lang_name.' Category Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'components', 'name' => 'components', 'title' => 'Components']);
            array_push($tableColumns, ['data' => 'child', 'name' => 'child', 'title' => 'Sub Category Name', 'orderable' => false, 'searchable' => false]);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);
            $module_name ='Category';
            return view('maestro.users.index', compact('html','module_name'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }


        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('maestro.users.create');
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
            if ($this->createUser($request)) {
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
            $user = $this->getUserById($id);
            if(empty($user)){
                return redirect()->route('users.index')->with(['error' => 'User not found.']);
            }
            return view('maestro.users.view', compact('user'));
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
            $user = $this->getUserById($id);
            if(empty($user)){
                return redirect()->route('users.index')->with(['error' => 'User not found.']);
            }
            return view('maestro.users.edit', compact('user'));
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
            if ($this->updateUserById($id,$request)) {
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
            if ($this->deleteUserById($id)) {
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
