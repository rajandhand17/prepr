<?php

namespace App\Http\Controllers\Api\public\Organization;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Public\Organization\OrganizationRepository;
use Illuminate\Http\Request;
use App\Http\Resources\Public\Organization\OrganizationResource as PublicOrganizationResource;

class OrganizationController extends AppBaseController
{
    private $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function index(Request $request){
        try {
            $organization = $this->organizationRepository->getOrganizationList($request);
            if ($organization !== false) {
                $response = [
                    'total_count'  => $organization->total(),
                    'per_page'     => $organization->perPage(),
                    'count'        => $organization->count(),
                    'current_page' => $organization->currentPage(),
                    'total_pages'  => $organization->lastPage(),
                    'list'         => PublicOrganizationResource::collection($organization),
                ];
                return $this->sendResponse([$response], __('responses.found_organization_list'));
            }
            return $this->sendError(__('responses.not_found_organization_list'), 400);
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if ($organization) {
                return $this->sendResponse(PublicOrganizationResource::make($organization), __('responses.found_organization_list'));
            }
            return $this->sendError(__('responses.organization_not_exists'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function follow($slug){
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if($organization){
                $checkFollowed=$this->organizationRepository->checkFollowUnfollowExists($organization->id,"1");
                if($checkFollowed!==false && $checkFollowed->follow_unfollow=="1"){
                    return $this->sendError(__('responses.already_followed_organization'),400);
                }
                $follow=$this->organizationRepository->follow($organization->id);
                if($follow){
                    return $this->sendResponse([],__('responses.follow_organization_successfully'));
                }
            }
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function unFollow($slug){
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if($organization){
                $checkFollowed=$this->organizationRepository->checkFollowUnfollowExists($organization->id,"2");
                if($checkFollowed!==false && $checkFollowed->follow_unfollow=="2"){
                    return $this->sendError(__('responses.already_unfollowed_organization'),400);
                }
                $follow=$this->organizationRepository->unfollow($organization->id);
                if($follow){
                    return $this->sendResponse([],__('responses.unfollow_organization_successfully'));
                }
            }
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function like($slug){
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if($organization){
                $checkLike=$this->organizationRepository->checkLikeUnlikeExists($organization->id,"1");
                if($checkLike!==false && $checkLike->like_dislike=="1"){
                    return $this->sendError(__('responses.already_like_organization'),400);
                }
                $like=$this->organizationRepository->like($organization->id);
                if($like){
                    return $this->sendResponse([],__('responses.follow_organization_successfully'));
                }
            }
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function unlike($slug){
        try {
            $organization = $this->organizationRepository->getOrganizationBasedOnSlug($slug);
            if($organization){
                $checkLike=$this->organizationRepository->checkLikeUnlikeExists($organization->id,"2");
                if($checkLike!==false && $checkLike->like_dislike=="2"){
                    return $this->sendError(__('responses.already_unlike_organization'),400);
                }
                $like=$this->organizationRepository->unlike($organization->id);
                if($like){
                    return $this->sendResponse([],__('responses.unlike_organization_successfully'));
                }
            }
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
}
