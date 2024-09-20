<?php

namespace App\Http\Controllers\Maestro\tag;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Tag\TagTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class TagController extends Controller
{
    use TagTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $tags = Tag::orderBy('id', 'DESC');
            if (request()->ajax()) {
                $i = 1;

                return DataTables::eloquent($tags)
                    ->addIndexColumn()
                    ->addColumn('action', static function (Tag $tags) {
                        $html = '';
                        $html .= '<a href="'.route('tags.show', ['tag' => $tags->id]).'" class="mr-25 showUser" data-id="'.$tags->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="'.route('tags.edit', ['tag' => $tags->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$tags->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="javascript:void(0)" onclick="deleteTag(\''.route('tags.destroy', ['tag' => $tags->id]).'\')"> <i class="fas fa-trash"></i></a>';

                        return $html;
                    })
                         ->editColumn('category', static function (Tag $tag) {
                             return implode(', ', array_map('ucfirst', explode(',', $tag->components)));
                         })

                         ->editColumn('tag_image', static function (Tag $tagImageData) {
                            $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';
    
                            return "<img src='".$tagImageData->tag_image."' width='50px' ".$onerror.'>';
                        })
                    ->editColumn('id', function (Tag $tag) {
                        if ($tag->id === 0 || $tag->id === '') {
                            return 'Admin';
                        } else {
                            return $tag->id ?? ' - ';
                        }
                    })
                    ->rawColumns(['tag_image', 'category', 'action', 'DT_Row_Index'])
                    ->toJson();
            }
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => '', 'title' => 'id'],
            ];
            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Tag Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'category', 'name' => 'category', 'title' => 'category', 'orderable' => false, 'searchable' => false]);
            array_push($tableColumns, ['data' => 'tag_image', 'name' => 'tag_image', 'title' => 'Tag Image', 'width' => '10%', 'orderable' => false, 'searchable' => false]);

            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);
            view()->share('module_name', 'Challenge');
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.tags.tag.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $category = Tag::pluck('title', 'id');

            return view('maestro.tags.tag.create', compact('languages', 'category'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createTag($request)) {
                return redirect()->route('tags.index')->with('success', 'Tag created successfully');
            }

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $tag = $this->getTagById($id);
            $languages = LanguageService::getAllActiveLanguages();
            if (!$tag->exists) {
                return redirect()->route('tags.index')->with(['error' => 'Tag not found.']);
            }

            return view('maestro.tags.tag.view', compact('tag', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = Tag::find($id);
            $languages = LanguageService::getAllActiveLanguages();
            $tag_image = Tag::where('id', '=', $id)->value('tag_image');
            $category = explode(',', $data->components);

            return view('maestro.tags.tag.edit', compact('data', 'languages', 'category', 'tag_image'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateTagById($id, $request)) {
                return redirect()->route('tags.index')->with('success', 'Tag Updated successfully');
            }

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tags.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteTagById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
