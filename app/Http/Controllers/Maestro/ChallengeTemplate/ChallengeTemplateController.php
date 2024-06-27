<?php

namespace App\Http\Controllers\Maestro\ChallengeTemplate;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeTemplate;
use App\Services\Maestro\Category\CategoryService;
use App\Services\Maestro\User\UserService;
use App\Traits\Maestro\Challenge\ChallengeTrait;
use App\Traits\Maestro\ChallengeTemplate\ChallengeTemplateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ChallengeTemplateController extends Controller
{
    use ChallengeTemplateTrait;
    public function __construct()
    {
        $this->middleware('web');
    }


    public function index(Builder $builder, Request $request)
    {
        try {
            $challengeTemplateInfo = $this->getChallengeTemplate();
            if (!empty($challengeTemplateInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($challengeTemplateInfo)
                        ->editColumn('status', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->status){
                                case '0';
                                    $html = "<span class='badge badge-success'>Draft</span>";
                                    break;
                                case '1';
                                    $html = "<span class='badge badge-success'>Published</span>";
                                    break;
                                case '2';
                                    $html = "<span class='badge badge-success'>Archive</span>";
                                    break;
                            }
                            return $html;
                        })->editColumn('privacy', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->privacy){
                                case '0';
                                    $html = "Public";
                                    break;
                                case '1';
                                    $html = "Private";
                                    break;
                            }
                            return $html;
                        })->editColumn('status', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->is_open){
                                case '0';
                                    $status = "Open";
                                    break;
                                case '1';
                                    $status = "Close";
                                    break;
                                case '2';
                                    $status = "Completed";
                                    break;
                            }
                            return $status;
                        })->editColumn('username', static function (ChallengeTemplate $challengeTemplateInfo) {
                            $user=UserService::getUserById($challengeTemplateInfo->user_id);
                            return $user->username;
                        })
                        ->addColumn('action', static function (ChallengeTemplate $challengeTemplateInfo) {
                            return '<a href="javascript:void(0)" onclick="deleteChallengeTemplate(\'' . route('challenge-template.destroy', ['challenge_template' => $challengeTemplateInfo->id]) . '\')"> <i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['status','category','username','action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'CHALLENGE TITLE', "width" => "5%"],
                ['data' => 'username', 'name' => 'username', 'title' => 'USERNAME', "width" => "10%"],
                ['data' => 'status', 'name' => 'status', 'title' => 'STATUS', "width" => "10%"],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'PUBLISHED', "width" => "10%"],
                ['data' => 'action', 'name' => 'action', 'title' => 'ACTION', "width" => "10%"],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.challengeTemplate.index', compact('html'));
        }catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteChallengeTemplateById($id)) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Challenge Template deleted successfully']);
            }
            DB::rollback();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

}
