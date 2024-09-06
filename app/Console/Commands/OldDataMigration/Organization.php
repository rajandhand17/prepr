<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationAddress;
use App\Models\OrganizationCustomization;
use App\Models\OrganizationMember;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;

class Organization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old organization table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for organizations table.');
            DB::beginTransaction();

            $organizations = DB::connection('mysql2')->table('organisations')->get();
            if ($organizations->count() > 0) {
                foreach ($organizations as $key => $organization) {
                    $checkUser = \App\Models\User::find($organization->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organizationDetails = DB::connection('mysql2')->table('organisations_details')->where('organisations_id', $organization->id)->first();
                    $organizationCustomizations = DB::connection('mysql2')->table('organization_customizations')->where('organization_id', $organization->id)->first();
                    $organizationPeoples = DB::connection('mysql2')->table('peoples')->where('organisation', $organization->id)->get();

                    $checkOrganization = \App\Models\Organization::find($organization->id);
                    if ($checkOrganization) {
                        $newOrganization = $checkOrganization;
                    } else {
                        $newOrganization = new \App\Models\Organization();
                    }
                    $category = null;
                    if ($organization->category != '0' && $organization->category != null) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($organization->category);
                        $checkCategory = \App\Models\Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }

                    switch ($organization->bussiness_challenge_option) {
                        case 'sales_marketing':
                            $bussiness_challenge_option = '1';
                            break;

                        case 'human_resources':
                            $bussiness_challenge_option = '2';
                            break;

                        case 'it_management':
                            $bussiness_challenge_option = '3';
                            break;

                        case 'customer_service':
                            $bussiness_challenge_option = '4';
                            break;

                        case 'research_development':
                            $bussiness_challenge_option = '5';
                            break;

                        case 'business_evelopment':
                            $bussiness_challenge_option = '6';
                            break;

                        case 'sustainability_and_environmental_management':
                            $bussiness_challenge_option = '7';
                            break;

                        default:
                            $bussiness_challenge_option = null;
                            break;
                    }
                    $newOrganization->id = $organization->id;
                    $newOrganization->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newOrganization->language = $organization->language;
                    $newOrganization->user_id = $organization->user_id;
                    $newOrganization->title = $organization->name;
                    $newOrganization->display_name = $organization->name;
                    $newOrganization->description = isset($organization->description) ? $organization->description : null;
                    $newOrganization->slug = $organization->slug;
                    $newOrganization->cover_image = (!empty($organization->cover_image)) ? $organization->cover_image : config('site-settings.default_organization_cover_image');
                    $newOrganization->profile_image = (!empty($organization->profile_image)) ? $organization->profile_image : config('site-settings.default_organization_profile_image');
                    $newOrganization->custom_url = isset($organization->vanity_slug) ? $organization->vanity_slug : null;
                    $newOrganization->website = isset($organization->website) ? $organization->website : null;
                    $newOrganization->about = isset($organization->about) ? $organization->about : null;
                    $newOrganization->category = $category;
                    $newOrganization->status = ($organization->status == '0') ? '0' : (($organization->status == '1') ? '1' : '3');
                    $newOrganization->total_employees = isset($organizationDetails->number_employees) ? $organizationDetails->number_employees : 0;
                    $newOrganization->is_verified = $organization->is_verified;
                    $newOrganization->business_challenge_tacklings = $bussiness_challenge_option;
                    $newOrganization->save();
                    $checkUser->attachRole('organization_owner', $newOrganization->id);

                    $checkOrganizationAddress = OrganizationAddress::where('organization_id', $organization->id)->first();
                    if ($checkOrganization) {
                        $organization_address = $checkOrganizationAddress;
                    } else {
                        $organization_address = new OrganizationAddress();
                    }

                    $organization_address->organization_id = $newOrganization->id;
                    $organization_address->latitude = isset($organization->latitude) ? $organization->latitude : null;
                    $organization_address->longitude = isset($organization->longitude) ? $organization->longitude : null;
                    $organization_address->full_address = isset($organizationDetails->address_one) ? $organizationDetails->address_one.', '.$organizationDetails->address_two : null;
                    $organization_address->address_1 = isset($organizationDetails->address_one) ? $organizationDetails->address_one : null;
                    $organization_address->address_2 = isset($organizationDetails->city) ? $organizationDetails->city : null;
                    $organization_address->city = isset($organizationDetails->city) ? $organizationDetails->city : null;
                    $organization_address->state = isset($organizationDetails->province) ? $organizationDetails->province : null;
                    $organization_address->country = isset($organizationDetails->country) ? $organizationDetails->country : null;
                    $organization_address->zip_code = isset($organizationDetails->postal_code) ? $organizationDetails->postal_code : null;
                    $organization_address->save();

                    if ($organizationCustomizations) {
                        $enable_custom_login_and_registration = '0';
                        if ($organizationCustomizations->enable_custom_login_and_registration == '1') {
                            $enable_custom_login_and_registration = '1';
                        }
                        $use_main_org_logo = '0';
                        if ($organizationCustomizations->use_main_org_logo == '1') {
                            $use_main_org_logo = '1';
                        }
                        $oldOrganizationCustomizations = new OrganizationCustomization();
                        $oldOrganizationCustomizations->organization_id = $newOrganization->id;
                        $oldOrganizationCustomizations->enable_custom_login_and_registration = $enable_custom_login_and_registration;
                        $oldOrganizationCustomizations->use_main_org_logo = $use_main_org_logo;
                        $oldOrganizationCustomizations->custom_logo_image = $organizationCustomizations->custom_logo_image;
                        $oldOrganizationCustomizations->custom_hero_image = $organizationCustomizations->custom_hero_image;
                        $oldOrganizationCustomizations->custom_background_color = $organizationCustomizations->custom_background_color;
                        $oldOrganizationCustomizations->save();
                    }

                    if ($organizationPeoples) {
                        OrganizationMember::where('organization_id', $organization->id)->delete();
                        foreach ($organizationPeoples as $organizationPeople) {
                            $organization_member = new OrganizationMember();
                            $organization_member->organization_id = $newOrganization->id;
                            $organization_member->name = $organizationPeople->name;
                            $organization_member->position = $organizationPeople->description;
                            $organization_member->image = $organizationPeople->image;
                            $organization_member->save();
                        }
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for organizations table completed.');

                return;
            }
            DB::rollback();
            $this->error('No organizations found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
