<?php

namespace App\Http\Controllers\Api\organization;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\Api\Organization\OrganizationRepository;
use App\Http\Resources\Organization\OrganizationResource;
class OrganizationController extends AppBaseController
{   
    private $organizationRepository;
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }
    public function getOrganization(Request $request)
    {  
       try {
          $organization=$this->organizationRepository->getOrganization($request);
          if ($organization) {
            return $this->sendResponse(OrganizationResource::collection($organization), __('responses.found_organizations_list'));
        }
        return $this->sendError(__('responses.found_not_organizations_list'));
         
       } catch (\Exception $e) {
         return $this->sendError(__('responses.send_error'), 500);
     }
    }
}
