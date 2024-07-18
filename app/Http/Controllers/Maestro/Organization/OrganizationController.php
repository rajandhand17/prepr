<?php

namespace App\Http\Controllers\Maestro\Organization;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Organization;
use App\Models\OrganizationAddress;
use App\Models\OrganizationMember;
use App\Models\OrganizationSocialLink;
use App\Models\SocialLink;
use App\Models\User;
use App\Traits\Maestro\Organization\OrganizationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class OrganizationController extends Controller
{
    use OrganizationTrait;

    public function construct()
    {
        $categories = Category::pluck('title', 'id')->prepend('Category', '');
        //dd( $categories );
        $status_array = [];
        $status_array['0'] = 'Inactive';
        $status_array['1'] = 'Active';
        View::share('status_array', $status_array);
        View::share('categories', $categories);
        View::share('module_name', 'Organization');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder)
    {
        try {
            $this->construct();
            $organizations = $this->getOrganizations();
            $organizations = Organization::orderBy('id', 'desc');
            //dd($organizations);
            if (request()->ajax()) {
                return DataTables::eloquent($organizations)
                    ->editColumn('profile_image', function (Organization $organizations) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('front/img/no-img.jpg').'";';

                        return "<img src='".asset($organizations->profile_image)."' width='100px' ".$onerror.'>';
                    })
                    ->editColumn('cover_image', function (Organization $organizations) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('front/img/no-img.jpg').'";';

                        return "<img src='".asset($organizations->cover_image)."' width='100px' ".$onerror.'>';
                    })
                    ->editColumn('website', function (Organization $organizations) {
                        return "<a class='longWrap' target='_blank' title=".$organizations->website.' href='.$organizations->website.'>'.$organizations->website.'</a>';
                    })
                    ->addColumn('action', static function (Organization $organizations) {
                        return '<input type="checkbox" name="verifiedorgs" value="'.$organizations->id.'" data-val="'.$organizations->is_verified.'" class="form-check-input" id="verifiedorgs" '.($organizations->is_verified == '1' ? 'checked' : '').'><a href="'.route('organization.edit', ['organization' => $organizations->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$organizations->id.'"><i class="fas fa-edit"></i></a><a href="javascript:void(0)" onclick="deleteOrg(\''.route('organization.destroy', ['organization' => $organizations->id]).'\')"> <i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['profile_image', 'cover_image', 'action', 'website'])
                    ->toJson();
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
                ['data' => 'title', 'name' => 'title', 'title' => 'title'],
                ['data' => 'profile_image', 'name' => 'profile_image', 'title' => 'Profile Image'],
                ['data' => 'cover_image', 'name' => 'cover_image', 'title' => 'Cover Image'],
                ['data' => 'website', 'name' => 'website', 'title' => 'Website', 'width' => '5%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]);
            View::share('module_name', 'Project Type');

            return view('maestro.organization.index', compact('html'));
        } catch (Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $organization = [];

            $this->construct();
            //$this->orgData();
            $org_user = [];
            $data = collect();
            $orgSocialLink = [];
            $categories = Category::pluck('title', 'id')->prepend('Select category', '');
            $users = User::pluck('username', 'id');
            $social_name = SocialLink::pluck('title', 'id')->prepend('Social', '')->toArray();
            unset($social_name[15]);
            View::share('org_user', $org_user);
            View::share('social_name', $social_name);
            $languages = Language::where(['status' => 1])->pluck('name', 'iso');

            return view('maestro.organization.create', compact('data', 'orgSocialLink', 'languages', 'categories', 'users'));
        } catch (Exception $e) {
            dd($e);
            DB::rollback();

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->construct();
            if ($this->createOrganization($request)) {
                DB::commit();

                return redirect()->route('organization.index')->with('success', 'Organization created successfully');
            }
            DB::rollback();

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $this->construct();
            $data = Organization::find($id);
            $org_user = User::where('id', $data->user_id)->pluck('username', 'id');
            $users = User::pluck('username', 'id');
            $orgSocialLink = OrganizationSocialLink::where('organization_id', $id)->get();
            $org_members = OrganizationMember::where('organization_id', $id)->get();
            foreach ($orgSocialLink as $value) {
                $social_name = SocialLink::where('id', $value->social_link_id)->first();
                $value->link_name = (!empty($social_name->title)) ? $social_name->title : '';
            }
            $social_name = SocialLink::pluck('title', 'id')->prepend('Social', '')->toArray();
            $org_address = OrganizationAddress::where('organization_id', $id)->get();
            unset($social_name[15]);
            View::share('social_name', $social_name);
            View::share('org_user', $org_user);
            View::share('people', '');
            $languages = Language::where(['status' => 1])->pluck('name', 'iso');

            return view('maestro.organization.edit', compact('data', 'orgSocialLink', 'languages', 'org_address', 'org_members', 'users'));
        } catch (Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $this->construct();
            if ($this->updateOrganizationById($id, $request)) {
                DB::commit();

                return redirect()->route('organization.index')->with('success', 'Organization Updated successfully');
            }
            DB::rollback();

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $this->construct();
            if ($this->deleteOrganizationById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Organization deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            dd($e);
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    public function verifyingOrgs(Request $request)
    {
        try {
            if ($request->org_v == '0') {
                Organization::where('id', $request->org_id)->update(['is_verified' => '1']);
                $message = 'Organization has been successfully verified.';
            } else {
                Organization::where('id', $request->org_id)->update(['is_verified' => '0']);
                $message = 'Organization verification has been removed.';
            }

            return response()->json(['status' => 'success', 'message' => $message], 200);
        } catch (Exception $e) {
            $message = __('notification.notification_sww');

            return response()->json(['status' => 'error', 'message' => $message], 500);
        }
    }
}
