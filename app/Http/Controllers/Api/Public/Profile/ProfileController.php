<?php

namespace App\Http\Controllers\Api\Public\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\AddCountryListResource;
use App\Http\Resources\AddInstitutionsResource;
use App\Repositories\Api\Public\Profile\ProfileRepository;
use Illuminate\Http\Request;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getCountries(Request $request)
    {
        try {
            $getCountryList = $this->profileRepository->getCountriesList($request->language, $request->search);
            if ($getCountryList) {
                return $this->sendResponse(AddCountryListResource::make($getCountryList), __('responses.country_list_fetched_successfully'));
            }

            return $this->sendError(__('responses.countries_fetched_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getInstitutions(Request $request)
    {
        try {
            $getCountryList = $this->profileRepository->getInstitutionsList($request->language, $request->search);
            if ($getCountryList) {
                return $this->sendResponse(AddInstitutionsResource::make($getCountryList), __('responses.country_list_fetched_successfully'));
            }

            return $this->sendError(__('responses.countries_fetched_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
