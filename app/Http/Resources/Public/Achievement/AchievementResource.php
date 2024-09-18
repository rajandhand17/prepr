<?php

namespace App\Http\Resources\Public\Achievement;

use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Public\LabProgramService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activity = null;
        $challenge_name = null;
        $project_name = null;
        $issued_by = $this->module_parent_title; // Setting preprlabs as default issued_by.
        $issuer_username = $this->module_parent_title; // Setting default issuer name based on data fetched from User Achievement Table
        $issuer_link = config('site-settings.frontend_site_url'); // Setting Default site url.
        $associated_data = [];

        switch($this->achievement_type) {
            case '0':
                $activity = 'lab';
                $getLab = LabService::getLabBasedOnId($this->module_id);
                if ($getLab) {
                    $associated_data = [
                        'name'            => $getLab->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'lab/'.$getLab->slug,
                    ];
                }
                $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($this->module_parent_id);
                if ($getIssuerData) {
                    $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                    $getUser = UserService::getUserById($getIssuerData->user_id);
                    $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                }
                $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                break;
            case '1':
                $activity = 'lab-program';
                $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($this->module_parent_id);
                if ($getIssuerData) {
                    $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                    $getUser = UserService::getUserById($getIssuerData->user_id);
                    $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    $getLabProgram = LabProgramService::getLabProgramBasedOnId($this->module_id);
                    $associated_data = [
                        'name'            => $getLabProgram->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'lab-program/'.$getLabProgram->slug,
                    ];
                }
                $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                break;
            case '2':
                $activity = 'challenge';
                $getChallengeData = ChallengeService::getChallengeBasedOnId($this->module_parent_id);
                if ($getChallengeData) {
                    $challenge_name = $getChallengeData->title ?? $this->module_parent_title;
                    $project_name = 'Static Preprlabs Project';
                    $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($getChallengeData->organization_id);
                    if ($getIssuerData) {
                        $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                        $getUser = UserService::getUserById($getIssuerData->user_id);
                        $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    }
                    $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                }
                break;
            case '3':
                $activity = 'challenge-path';
                $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($this->module_parent_id);
                if ($getIssuerData) {
                    $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                    $getUser = UserService::getUserById($getIssuerData->user_id);
                    $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    $getChallengePath = ChallengePathService::getChallengePathBasedOnId($this->module_id);
                    $associated_data = [
                        'name'            => $getChallengePath->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'challenge-path/'.(isset($getChallengePath->slug) ? $getChallengePath->slug : ''),
                    ];
                }
                $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                break;
            case '4':
                $activity = 'resource-group';
                $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($this->module_parent_id);
                if ($getIssuerData) {
                    $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                    $getUser = UserService::getUserById($getIssuerData->user_id);
                    $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    $getResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($this->module_id);
                    $associated_data = [
                        'name'            => $getResourceGroup->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'resource-group/'.$getResourceGroup->slug,
                    ];
                }
                $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                break;
            case '5':
                $activity = 'appreciation';
                break;
            case '6':
                $activity = 'activity';
                break;
            case '7':
                $activity = 'skill-activity';
                break;
            case '8':
                $activity = 'imported';
                break;
            case '9':
                $activity = 'winner';
                $getChallengeData = ChallengeService::getChallengeBasedOnId($this->module_parent_id);
                if ($getChallengeData) {
                    $challenge_name = $getChallengeData->title ?? $this->module_parent_title;
                    $associated_data = [
                        'name'            => $getChallengeData->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'challenge/'.$getChallengeData->slug,
                    ];
                    $project_name = 'Static Preprlabs Project';
                    $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($getChallengeData->organization_id);
                    if ($getIssuerData) {
                        $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                        $getUser = UserService::getUserById($getIssuerData->user_id);
                        $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    }
                    $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                }
                break;
            case '10':
                $activity = 'participation';
                $getChallengeData = ChallengeService::getChallengeBasedOnId($this->module_parent_id);
                if ($getChallengeData) {
                    $challenge_name = $getChallengeData->title ?? $this->module_parent_title;
                    $associated_data = [
                        'name'            => $getChallengeData->title ?? $this->module_title,
                        'associated_link' => config('site-settings.frontend_site_url').'challenge/'.$getChallengeData->slug,
                    ];
                    $project_name = 'Static Preprlabs Project';
                    $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($getChallengeData->organization_id);
                    if ($getIssuerData) {
                        $issuer_link = config('site-settings.frontend_site_url').'organization/'.$getIssuerData->slug;
                        $getUser = UserService::getUserById($getIssuerData->user_id);
                        $issuer_username = UserService::joinName($getUser->first_name, $getUser->last_name) ?? $this->module_parent_title;
                    }
                    $issued_by = $getIssuerData->title ?? $this->module_parent_title;
                }
                break;
            default:
                $activity = null;
                break;
        }

        switch ($this->is_featured) {
            case '0':
                $is_featured = 'no';
                break;
            case '1':
                $is_featured = 'yes';
                break;
            default:
                $is_featured = 'no';
                break;
        }

        //Receiver name
        $issue_to = UserService::joinName($this->user->first_name, $this->user->last_name);

        return [
            'id'                    => $this->certificate_number,
            'issue_to'              => $issue_to,
            'award_name'            => $this->module_title,
            'issue_by'              => $issued_by,
            'issue_date'            => $this->issue_date,
            'description'           => $this->description,
            'issuer_username'       => $issuer_username,
            'issuer_link'           => $issuer_link,
            'is_featured'           => $is_featured,
            'associated_data'       => $associated_data,
            'challenge_title'       => $challenge_name,
            'project_title'         => $project_name,
            'microcopy'             => $this->title,
            'prize'                 => $this->achievement_prize,
            'points'                => $this->achievement_points,
            'image'                 => $this->achievement_image,
            'promo_code'            => $this->promo_code,
            'achievement_type'      => $activity,
        ];
    }
}
