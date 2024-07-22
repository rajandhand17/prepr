<?php

namespace App\Http\Controllers\Maestro\SocialLink;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Traits\Maestro\SocialLink\SocialLinkTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SocialLinkController extends Controller
{
    use SocialLinkTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $socialLinks = $this->getSocialLinkList();
            if (request()->ajax()) {
                return DataTables::eloquent($socialLinks)
                    ->addIndexColumn()
                    ->editColumn('title', static function (SocialLink $socialLinks) {
                        return $socialLinks->title;
                    })
                    ->editColumn('icon', static function (SocialLink $socialLinks) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='".asset($socialLinks->icon)."' width='30px' ".$onerror.'>';
                    })

                    ->addColumn('action', static function (SocialLink $socialLinks) {
                        return '<a class="mr-10" href="'.route('social-links.edit', ['social_link' => $socialLinks->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteSocialLink(\''.route('social-links.destroy', ['social_link' => $socialLinks->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['icon', 'action', 'DT_Row_Index'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Social Media Name', 'width' => '85%'],
                ['data' => 'icon', 'name' => 'icon', 'title' => 'Icon', 'width' => '5%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ])->parameters([
                'order' => [[1, 'asc']],
            ]);

            return view('maestro.sociallink.index', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('maestro.sociallink.create');
        } catch (Exception $e) {
            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createSocialLink($request)) {
                DB::commit();

                return redirect()->route('social-links.index')->with('success', 'SocialLink created successfully');
            }
            DB::rollback();

            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $socialLink = $this->getSocialLinkById($id);
            if (!$socialLink->exists) {
                return redirect()->route('social-links.index')->with(['error' => 'SocialLink not found.']);
            }

            return view('maestro.sociallink.edit', compact('socialLink'));
        } catch (Exception $e) {
            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateSocialLinkById($id, $request)) {
                DB::commit();

                return redirect()->route('social-links.index')->with('success', 'SocialLink Updated successfully');
            }
            DB::rollback();

            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('social-links.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSocialLinkById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'SocialLink deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
