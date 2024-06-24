<?php

namespace App\Http\Controllers\Maestro\Tag;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\TagGroup;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Traits\Maestro\Tag\TagGroupTrait;
use Exception;

class TagGroupController extends Controller
{
    use TagGroupTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    public function index(Builder $builder, Request $request)
    {
        try {
            $tagGroup = TagGroup::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::eloquent($tagGroup)
                ->addColumn('action', static function ($tagGroup) {
                    $html = '';
                    $html .= '<a href="' . route('taggroup.show', ['taggroup' => $tagGroup->id]) . '" class="mr-25 showUser" data-id="' . $tagGroup->id . '"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="' . route('taggroup.edit', ['taggroup' =>  $tagGroup->id]) . '" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="' . $tagGroup->id . '"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" onclick="deleteTagGroup(\'' . route('taggroup.destroy', ['taggroup' => $tagGroup->id]) . '\')"> <i class="fas fa-trash"></i></a>';
                    return $html;
                })
                ->editColumn('tags', static function ($tagGroup) {
                    $tags = $tagGroup->tags;
                    $tag_names = [];
                    foreach ($tags as $tag_name) {
                        if (Tag::where('id', $tag_name)->get()->count() > 0) {
                            $tag_names[] = Tag::find($tag_name)->title;
                        } else {
                            return "Tag doesn't exist";
                        }
                    }
                    return implode(', ', $tag_names);
                })
                ->toJson();
            }
            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ];
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'description';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName . '_title';
                    $columName2 = $columName.'_description';
                }
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Tag Group Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Tag Group Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'tags', 'name' => 'Tags', 'title' => 'Tags']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);


            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);
            $languages = Language::where('status', 1)->get();
            return view('maestro.tags.taggroup.index', compact('html', 'languages'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('dashboard.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = Language::where('status', 1)->get();
            $tags = Tag::orderBy('id', 'DESC')->pluck('title', 'id')->take(50);
            //dd($skills);
            $selectedTags = [];
            return view('maestro.tags.taggroup.create', compact('languages', 'tags', 'selectedTags'));
        } catch (Exception $e) {
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createTagGroup($request)) {
                DB::commit();
                return redirect()->route('taggroup.index')->with('success', 'Tag Group created successfully');
            }
            DB::rollback();
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
           
            $taggroup = $this->getTagGroupById($id);
            $selectedTags = [];
            foreach ($taggroup->tags as $tag_name) {
                if (Tag::where('id', $tag_name)->get()->count() > 0) {
                    $tag_names[] = Tag::find($tag_name)->title;
                } else {
                    $selectedTags = "Tag doesn't exist";
                }
            }
            $selectedTags = implode(', ', $tag_names);
            $languages = Language::where('status', 1)->get();
            if(!$taggroup->exists){
                return redirect()->route('taggroup.index')->with(['error' => 'Skill not found.']);
            }
            return view('maestro.tags.taggroup.view', compact('taggroup', 'languages','selectedTags'));
        } catch (Exception $e) {
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = TagGroup::find($id);
            $selectedTags = [];
            foreach ( $data->tags as $tags) {
                $selectedTags[] = $tags;
            }
            $title = $data->title;
            $description = $data->description;
            $tags = Tag::pluck('title', 'id');
         
            $languages = Language::where('status', 1)->get();
            return view('maestro.tags.taggroup.edit', compact('tags', 'selectedTags', 'title', 'description', 'languages', 'data'));
        } catch (Exception $e) {
            dd($e);
            redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateTagGroupById($id,$request)) {
                DB::commit();
                return redirect()->route('taggroup.index')->with('success', 'Tag Group Updated successfully');
            }
            DB::rollback();
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteTagGroupById($id)) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
