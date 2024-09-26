<?php

namespace App\Http\Controllers\Maestro\Tag;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\TagService;
use App\Traits\Maestro\Tag\TagGroupTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class TagGroupController extends Controller
{
    use TagGroupTrait;

    public function index(Builder $builder, Request $request)
    {
        try {
            $tagGroup = TagGroup::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::eloquent($tagGroup)
                ->addColumn('action', static function ($tagGroup) {
                    $html = '';
                    $html .= '<a href="'.route('tag-group.show', $tagGroup->id).'" class="mr-25 showUser" data-id="'.$tagGroup->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="'.route('tag-group.edit', $tagGroup->id).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$tagGroup->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" onclick="deleteTagGroup(\''.route('tag-group.destroy', $tagGroup->id).'\')"> <i class="fas fa-trash"></i></a>';

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
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ];
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Tag Group Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Tag Group Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'tags', 'name' => 'Tags', 'title' => 'Tags']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);

            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);

            return view('maestro.tags.tag-group.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('dashboard.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $tags = TagService::getTags();
            $selectedTags = [];

            return view('maestro.tags.tag-group.create', compact('languages', 'tags', 'selectedTags'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createTagGroup($request)) {
                return redirect()->route('tag-group.index')->with('success', 'Tag Group created successfully');
            }

            return redirect()->route('tag-group.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tag-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $taggroup = $this->getTagGroupById($id);
            $selectedTags = TagService::getSelectedTagByIds($taggroup->tags);
            $languages = LanguageService::getAllActiveLanguages();
            if (!$taggroup->exists) {
                return redirect()->route('tag-group.index')->with(['error' => 'Tag not found.']);
            }

            return view('maestro.tags.tag-group.view', compact('taggroup', 'languages', 'selectedTags'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tag-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = $this->getTagGroupById($id);
            $selectedTags = [];
            foreach ($data->tags as $tags) {
                $selectedTags[] = $tags;
            }
            $tags = TagService::getTags();
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.tags.tag-group.edit', compact('tags', 'selectedTags', 'languages', 'data'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            redirect()->route('taggroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateTagGroupById($id, $request)) {
                return redirect()->route('tag-group.index')->with('success', 'Tag Group Updated successfully');
            }

            return redirect()->route('tag-group.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('tag-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteTagGroupById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
