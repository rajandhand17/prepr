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
        // Default values
        $activity = null;
        $challenge_name = null;
        $project_name = null;
        $issued_by = $this->module_parent_title;
        $issuer_username = $this->module_parent_title;
        $issuer_link = config('site-settings.frontend_site_url');
        $associated_data = [];

        // Helper to set issuer data
        $issuerData = $this->getIssuerData($this->module_parent_id);
        if ($issuerData) {
            $issuer_link = config('site-settings.frontend_site_url').'organization/'.$issuerData['slug'];
            $issuer_username = $issuerData['username'];
            $issued_by = $issuerData['title'];
        }

        // Handle achievement types
        $activity = $this->getActivityType($issuerData, $associated_data, $challenge_name, $project_name);

        // Handle featured status
        $is_featured = $this->is_featured === '1' ? 'yes' : 'no';

        // Receiver name
        $issue_to = UserService::joinName($this->user->first_name, $this->user->last_name);

        return [
            'id'               => $this->certificate_number,
            'issue_to'         => $issue_to,
            'award_name'       => $this->module_title,
            'issue_by'         => $issued_by,
            'issue_date'       => $this->issue_date,
            'description'      => $this->description,
            'issuer_username'  => $issuer_username,
            'issuer_link'      => $issuer_link,
            'is_featured'      => $is_featured,
            'associated_data'  => $associated_data,
            'challenge_title'  => $challenge_name,
            'project_title'    => $project_name,
            'microcopy'        => $this->title,
            'prize'            => $this->achievement_prize,
            'points'           => $this->achievement_points,
            'image'            => $this->achievement_image,
            'promo_code'       => $this->promo_code,
            'achievement_type' => $activity,
        ];
    }

    /**
     * Helper method to get issuer data.
     */
    private function getIssuerData($module_parent_id)
    {
        $getIssuerData = OrganizationService::getOrganizationExistBasedOnId($module_parent_id);
        if ($getIssuerData) {
            $getUser = UserService::getUserById($getIssuerData->user_id);

            return [
                'title'    => $getIssuerData->title,
                'slug'     => $getIssuerData->slug,
                'username' => UserService::joinName(optional($getUser)->first_name, optional($getUser)->last_name),
            ];
        }

        return null;
    }

    /**
     * Helper method to handle activity types.
     */
    private function getActivityType($issuerData, &$associated_data, &$challenge_name, &$project_name)
    {
        switch ($this->achievement_type) {
            case '0': // Lab
                $activity = 'lab';
                $getLab = LabService::getLabBasedOnId($this->module_id);
                $associated_data = $this->getAssociatedData($getLab, 'lab');
                break;

            case '1': // Lab Program
                $activity = 'lab-program';
                $getLabProgram = LabProgramService::getLabProgramBasedOnId($this->module_id);
                $associated_data = $this->getAssociatedData($getLabProgram, 'lab-program');
                break;

            case '2': // Challenge
            case '9': // Winner
            case '10': // Participation
                $activity = 'challenge';
                $getChallengeData = ChallengeService::getChallengeBasedOnId($this->module_parent_id);
                $challenge_name = $getChallengeData->title ?? $this->module_parent_title;
                $associated_data = $this->getAssociatedData($getChallengeData, 'challenge');
                $project_name = 'Static Preprlabs Project';
                break;

            case '3': // Challenge Path
                $activity = 'challenge-path';
                $getChallengePath = ChallengePathService::getChallengePathBasedOnId($this->module_id);
                $associated_data = $this->getAssociatedData($getChallengePath, 'challenge-path');
                break;

            case '4': // Resource Group
                $activity = 'resource-group';
                $getResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($this->module_id);
                $associated_data = $this->getAssociatedData($getResourceGroup, 'resource-group');
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

            default:
                $activity = null;
                break;
        }

        return $activity;
    }

    /**
     * Helper method to generate associated data.
     */
    private function getAssociatedData($data, $type)
    {
        if ($data) {
            return [
                'name'            => $data->title ?? $this->module_title,
                'associated_link' => config('site-settings.frontend_site_url')."$type/".($data->slug ?? ''),
            ];
        }

        return [];
    }
}
