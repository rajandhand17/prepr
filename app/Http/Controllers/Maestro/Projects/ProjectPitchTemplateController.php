<?php

namespace App\Http\Controllers\Maestro\Projects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Traits\Maestro\Project\ProjectPitchTemplateTrait;
use App\Models\PitchTemplate;
use App\Services\Maestro\LanguageService;
use Exception;

class ProjectPitchTemplateController extends Controller
{
    use ProjectPitchTemplateTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    public function index(Builder $builder)
    {
        $templates = $this->getPitchTemplate();
        if (request()->ajax()) {
            return DataTables::eloquent($templates)
              ->addColumn('action', function (PitchTemplate $templates) {
                return '<a style="padding-left:20px" class="mr-10" href="' . route('projects-pitch-template.edit', ['projects_pitch_template' => $templates->id]) . '"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deletePitchTemplate(\'' . route('projects-pitch-template.destroy', ['projects_pitch_template' => $templates->id]) . '\')"><i class="fas fa-trash"></i></a>';
              })
              ->toJson();
            }
        $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false],
            ];
            $columName = 'Pitch Template Title';
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
                    $columName = $columName . '_title';
                }
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name . ' Pitch Template Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);
        return view('maestro.projects.pitchtemplate.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            return view('maestro.projects.pitchtemplate.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
                DB::beginTransaction();
            if ($this->storePitchTemplate($request, '', 'create')) {
                DB::commit();
                return redirect()->route('projects-pitch-template.index')->with(['success' => 'Pitch Template Added successfully.']);
            }
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $pitchTemplate = $this->findPitchTemplate($id);
            $pitchSection = $this->getPitchSectionById($id);
            $pitchTask = $this->getPitchTaskById($id);
            return view('maestro.projects.pitchtemplate.edit', compact('languages','pitchTemplate','pitchSection','pitchTask'));
        } catch (Exception $e) {
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updatePitchTemplate($request, $id, 'update')) {
                DB::commit();
                return redirect()->route('projects-pitch-template.index')->with(['success' => 'Pitch Template updated successfully.']);
            }
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('projects-pitch-template.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $pitchTemplate = $this->findPitchTemplate($id);
            if (!empty($pitchTemplate)) {
                $this->deletePitchTemplate($pitchTemplate);
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Pitch Template deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
