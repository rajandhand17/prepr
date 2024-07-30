<?php

namespace App\Http\Controllers\Maestro\Sponsors;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Traits\Maestro\Sponsor\SponsorTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SponsorsController extends Controller
{
    use SponsorTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $sponsors = $this->getSponsorList();
            if (request()->ajax()) {
                return DataTables::eloquent($sponsors)
                    ->addIndexColumn()
                    ->editColumn('title', static function (Host $sponsors) {
                        return $sponsors->title;
                    })
                    ->editColumn('link', static function (Host $sponsors) {
                        return $sponsors->link;
                    })
                    ->editColumn('image', static function (Host $sponsors) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='".asset($sponsors->image)."' width='50px' ".$onerror.'>';
                    })
                    ->editColumn('status', static function (Host $sponsors) {
                        if ($sponsors->status == 1) {
                            $html = "<span class='badge badge-success'>Active</span>";
                        } else {
                            $html = "<span class='badge badge-danger'>InActive</span>";
                        }
                        return $html;
                    })
                    ->addColumn('action', static function (Host $sponsors) {
                        return '<a class="mr-10" href="'.route('sponsors.edit', ['sponsor' => $sponsors->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteSponsor(\''.route('sponsors.destroy', ['sponsor' => $sponsors->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['image', 'status','action', 'DT_Row_Index'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Sponsor Name', 'width' => '10%'],
                ['data' => 'link', 'name' => 'link', 'title' => 'Link', 'width' => '20%'],
                ['data' => 'image', 'name' => 'image', 'title' => 'Image', 'width' => '10%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'status', 'width' => '10%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ])->parameters([
                'order' => [[1, 'asc']],
            ]);

            return view('maestro.sponsors.index', compact('html'));
        } catch (Exception $e) {
            return response()->back()->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('maestro.sponsors.create');
        } catch (Exception $e) {
            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createSponsor($request)) {
                return redirect()->route('sponsors.index')->with('success', 'Sponsor created successfully');
            }

            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $sponsor = $this->getSponsorById($id);
            if (!$sponsor->exists) {
                return redirect()->route('sponsors.index')->with(['error' => 'Sponsor not found.']);
            }

            return view('maestro.sponsors.edit', compact('sponsor'));
        } catch (Exception $e) {
            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateSponsorById($id, $request)) {
                return redirect()->route('sponsors.index')->with('success', 'Sponsor Updated successfully.');
            }

            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteSponsorById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Sponsor deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
