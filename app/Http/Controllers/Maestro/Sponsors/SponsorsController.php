<?php

namespace App\Http\Controllers\Maestro\Sponsors;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Traits\Maestro\Sponsor\SponsorTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                        if ($sponsors->status == '1') {
                            $html = 'Active';
                        } else {
                            $html = 'Deactive';
                        }

                        return $html;
                    })
                    ->addColumn('action', static function (Host $sponsors) {
                        return '<a class="mr-10" href="'.route('sponsors.edit', ['sponsor' => $sponsors->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteSponsor(\''.route('sponsors.destroy', ['sponsor' => $sponsors->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['image', 'action', 'DT_Row_Index'])
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
            return response()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $sponsor_status = $this->getSponsorStatus();

            return view('maestro.sponsors.create', compact('sponsor_status'));
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
            DB::beginTransaction();
            if ($this->createSponsor($request)) {
                DB::commit();

                return redirect()->route('sponsors.index')->with('success', 'Sponsor created successfully');
            }
            DB::rollback();

            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

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
            $sponsor_status = $this->getSponsorStatus();

            return view('maestro.sponsors.edit', compact('sponsor', 'sponsor_status'));
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
            DB::beginTransaction();
            if ($this->updateSponsorById($id, $request)) {
                DB::commit();

                return redirect()->route('sponsors.index')->with('success', 'Sponsor Updated successfully');
            }
            DB::rollback();

            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('sponsors.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSponsorById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Sponsor deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
