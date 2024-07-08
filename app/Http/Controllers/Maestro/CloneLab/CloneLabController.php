<?php

namespace App\Http\Controllers\Maestro\CloneLab;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\Maestro\CloneLab\CloneLabTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

class CloneLabController extends Controller
{
    use CloneLabTrait;
    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $organizations=$this->getOrganization();
            $associativeLab=$this->getAllLabs();
            $languages = $this->getAllLanguages();
            return view('maestro.cloneLab.index', compact('organizations','languages','associativeLab'));
        }catch (\Exception $e) {
            return false;
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $getResponses=$this->createLab($request);
            if ($getResponses!==false) {
                DB::commit();
                return redirect()->route('clone-lab.index')->with('success', 'Lab created successfully');
            }
            DB::rollback();
            return redirect()->route('clone-lab.index')->with(['error' => 'Something want wrong.']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }
}
