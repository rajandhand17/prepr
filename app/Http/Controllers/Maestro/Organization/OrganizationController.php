<?php

namespace App\Http\Controllers\Maestro\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\OrganizationAddressService;
use App\Services\Maestro\OrganizationMemberService;
use App\Services\Maestro\OrganizationSocialLinkService;
use App\Services\Maestro\SocialLinkService;
use App\Traits\Maestro\Organization\OrganizationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class OrganizationController extends Controller
{
    use OrganizationTrait;

    public function construct()
    {
        $status_array = [];
        $status_array['0'] = 'Inactive';
        $status_array['1'] = 'Active';
        View::share('status_array', $status_array);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder)
    {
        try {
            $this->construct();
            $organizations = $this->getOrganizations();
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

            return view('maestro.organization.index', compact('html'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->construct();
            $data = collect();
            $languages = LanguageService::getLanguages();
            $social_name = SocialLinkService::getSocialLinkList();

            return view('maestro.organization.create', compact('data', 'languages', 'social_name'));
        } catch (Exception $e) {
            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->construct();
            if ($this->createOrganization($request)) {
                return redirect()->route('organization.index')->with('success', 'Organization created successfully');
            }

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
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
            $org_members = OrganizationMemberService::getOrganizationMembersById($id);
            $orgSocialLink = OrganizationSocialLinkService::getOrganizationSocialLink($id);
            $social_name = SocialLinkService::getSocialLinkList();
            $orgAssociatedItems = $this->getOrgAssociatedItemsById($data);
            $org_address = OrganizationAddressService::getOrganizationAddressById($id);
            $languages = LanguageService::getLanguages();

            return view('maestro.organization.edit', compact('data', 'orgSocialLink', 'languages', 'org_address', 'org_members', 'social_name', 'orgAssociatedItems'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->construct();
            if ($this->updateOrganizationById($id, $request)) {
                return redirect()->route('organization.index')->with('success', 'Organization Updated successfully');
            }

            return redirect()->route('organization.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            return redirect()->route('organization.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->construct();
            if ($this->deleteOrganizationById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Organization deleted successfully']);
            }
        } catch (Exception $e) {
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
