<?php

namespace App\Http\Controllers\Maestro\Explore;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\FeaturedModule;
use App\Models\Lab;
use App\Models\LabProgram;
use App\Models\Project;
use App\Models\ResourceModule;
use App\Traits\Maestro\Explore\ExploreTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

/*-----------------------------------------------------------------------------------------
@description: This controller is for handle explore data
@functions: show,create,edit,store,update,destroy
-----------------------------------------------------------------------------------------*/

class ExploreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use ExploreTrait;

    public function construct()
    {
        $this->middleware('web');
    }

    /**
     * Show the application dashboard.
     *
     * @param Builder $builder
     *
     * @return JsonResponse
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function for show all explore data
    @Output: Show all explore data on admin panel
    -------------------------------------------------------------------------------------------- */
    public function index(Builder $builder)
    {
        try {
            $data = FeaturedModule::get();

            return view('maestro.explore.index', compact('data'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => 'Something went wrong']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for share view of  edit explore data
    @input:id
    -------------------------------------------------------------------------------------------- */
    public function edit($id)
    {
        try {
            $component = FeaturedModule::find($id);
            $roles = $this->getAllRoles();
            $selected_role = json_decode($component->role, true); // true will convert it to an associative array

            return view('maestro.explore.edit', compact('component', 'roles', 'selected_role'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => 'Something went wrong']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for update  explore data
    @input: id, identifier, subject, content
    @Output: update explore data in database
    -------------------------------------------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        try {
            $this->construct();
            if ($this->updateExploreDataById($id, $request)) {
                return redirect()->route('explore.index')->with('success', 'Data has Updated successfully');
            }

            return redirect()->route('explore.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('explore.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for delete  explore data
    @input: id
    @Output: delete explore data in database
    -------------------------------------------------------------------------------------------- */
    public function destroy($id)
    {
        try {
            $this->construct();
            if ($this->deleteExploreDataById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Upload image from ck-editor.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $original = $request->file('upload')->getClientOriginalName();

            $fileName = pathinfo($original, PATHINFO_FILENAME);

            $extension = $request->file('upload')->getClientOriginalExtension();

            $fileName = $fileName.'_'.time().'.'.$extension;

            $url = $request->file('upload')->store('uploads', 's3');

            return response()->json(['fileName' => $fileName, 'uploaded' => true, 'url' => \Config::get('app.CloudFrontUrl').'/'.$url]);
        }
    }

    public function searchComponents(Request $request)
    { 
    try {
        $query = $request->get('query', '');
        $filter = $request->get('filter', ''); // Get the filter from request
        $exploreIds = FeaturedModule::pluck('module_id')->toArray();

        // Define a base query for each model
        $components = collect();
        $perPage = 10;
        $currentPage = $request->get('page', 1);

        if ($filter == '' || $filter == 'Lab') {
            $labs = Lab::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Lab';

                return $item;
            });
            $components = $components->merge($labs);
        }

        if ($filter == '' || $filter == 'Challenge') {
            $challenges = Challenge::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Challenge';

                return $item;
            });
            $components = $components->merge($challenges);
        }

        if ($filter == '' || $filter == 'Project') {
            $projects = Project::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Project';

                return $item;
            });
            $components = $components->merge($projects);
        }

        if ($filter == '' || $filter == 'Resource Module') {
            $resources = ResourceModule::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Resource Module';

                return $item;
            });
            $components = $components->merge($resources);
        }

        if ($filter == '' || $filter == 'Lab Program') {
            $labPrograms = LabProgram::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Lab Program';

                return $item;
            });
            $components = $components->merge($labPrograms);
        }

        if ($filter == '' || $filter == 'Challenge Path') {
            $challengePaths = ChallengePath::where('title', 'like', '%'.$query.'%')->whereNotIn('id', $exploreIds)->get()->map(function ($item) {
                $item->type = 'Challenge Path';

                return $item;
            });
            $components = $components->merge($challengePaths);
        }

        // Paginate the results
        $total = $components->count();
        $components = $components->slice(($currentPage - 1) * $perPage, $perPage);

        $html = view('maestro.explore.searchableItems', compact('components'))->render();

        return response()->json(['html' => $html, 'total' => $total, 'perPage' => $perPage, 'currentPage' => $currentPage]);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('explore.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function insertExploreData(Request $request)
    {
        try {
        if ($this->insertExploreDatas($request)) {
            return response()->json(['status' => 'success', 'message' => 'Data has been added successfully'], 200);
        }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('explore.index')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
