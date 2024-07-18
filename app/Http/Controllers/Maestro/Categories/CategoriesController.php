<?php

namespace App\Http\Controllers\Maestro\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Traits\Maestro\Category\CategoryTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class CategoriesController extends Controller
{
    use CategoryTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $categories = $this->getCategory();
            if (request()->ajax()) {
                return DataTables::eloquent($categories)
                    ->addColumn('action', static function (Category $category) {
                        return '<a style="padding-left:50px" class="mr-10" href="'.route('category.edit', ['category' => $category->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteCategory(\''.route('category.destroy', ['category' => $category->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->addColumn('child', static function (Category $category) {
                        return '<a style="padding-left:50px" class="mr-10" href="'.route('category.subcategory', ['id' => $category->id]).'">View Sub Category</a>';
                    })->rawColumns(['child', 'action'])
                    ->toJson();
            }

            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ];
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName = 'title';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName.'_title';
                }
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->lang_name.' Category Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'components', 'name' => 'components', 'title' => 'Components', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'child', 'name' => 'child', 'title' => 'Sub Category Name', 'orderable' => false, 'searchable' => false]);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '15%']);
            $html = $builder->columns($tableColumns);
            $module_name = 'Category';

            return view('maestro.categories.index', compact('html', 'module_name'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => 'Something went wrong.']);
        }

        return view('maestro.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();
            $category_list = $this->getCategories();

            return view('maestro.categories.create', compact('languages', 'category_list'));
        } catch (Exception $e) {
            return redirect()->route('category.index')->withErrors(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdateCategory($request, '', 'create')) {
                if ($request->filled('parent_id')) {
                    return redirect()->route('category.index')->with(['success' => 'Sub Category Added successfully']);
                } else {
                    return redirect()->route('category.index')->with(['success' => 'Category Added successfully']);
                }
            }

            return redirect()->route('category.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            return redirect()->route('category.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->getLanguage();
            $category_list = $this->getCategories();
            $category = $this->getCategories();
            $root = $this->getCategoryById($id);
            $components = $this->getComponentsById($root);
            $category = $this->getFirstCategoryById($id);

            return view('maestro.categories.edit', compact('root', 'category', 'components', 'category_list', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('category.index')->withErrors(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdateCategory($request, $id, 'update')) {
                if ($request->get('parent_id') !== null) {
                    return redirect()->route('category.index')->with(['success' => 'Sub Category updated successfully.']);
                } else {
                    return redirect()->route('category.index')->with(['success' => 'Category updated successfully.']);
                }
            }

            return redirect()->route('category.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            return redirect()->route('category.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = $this->findCategory($id);
            if ($category->parent_id === 0) {
                $checkPCate = Category::where('parent_id', $id)->count();
                if (!empty($checkPCate)) {
                    return response()->json(['status' => 'fail', 'message' => 'Please delete subcategory first.']);
                } else {
                    $this->deleteCategory($category);

                    return response()->json(['status' => 'success', 'message' => 'Category deleted successfully.']);
                }
            } else {
                $this->deleteCategory($category);

                return response()->json(['status' => 'success', 'message' => 'Subcategory deleted successfully.']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Record deleted successfully.']);
        }
    }

    /**
     * List sub-category the specified resource from storage.
     */
    public function getSubCategory($id, Builder $builder)
    {
        try {
            $categories = $this->getSubCategoryById($id);
            if (request()->ajax()) {
                return DataTables::eloquent($categories)
                    ->addColumn('action', static function (Category $category) {
                        return '<a style="padding-left:50px" class="mr-10" href="'.route('category.edit', ['category' => $category->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteSubCategory(\''.route('category.destroy', ['category' => $category->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->addColumn('PCategory', static function (Category $category) {
                        $parent_category = Category::where('id', $category->parent_id)->first();
                        if ($parent_category !== null) {
                            return $parent_category->title;
                        }

                        return '-';
                    })
                    ->toJson();
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'id', 'title' => 'Id', 'width' => '10%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'SubCategory Name'],
                ['data' => 'PCategory', 'name' => 'PCategory', 'title' => 'Parent Category', 'orderable' => false],
                ['data' => 'components', 'name' => 'components', 'title' => 'Components'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '15%'],
            ]);
            $module_name = 'Sub Category';

            return view('maestro.categories.sub-category-index', compact('html', 'module_name'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ]);
        }
    }
}
