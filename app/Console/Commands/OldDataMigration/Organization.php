<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Organization;
use App\Models\OrganizationAddress;
use App\Models\OrganizationCustomization;
use App\Models\OrganizationMember;
use App\Models\User;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class OrganizationMigration extends Command
{
    protected $signature = 'migrate-old-data:organizations';
    protected $description = 'Migrate old organization table data to new db structure.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting migration of old organization data...');
        DB::beginTransaction();

        try {
            $organizations = DB::connection('mysql2')->table('organisations')->get();

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
                    $newOrganization->vanity_slug = isset($organization->vanity_slug) ? $organization->vanity_slug : null;
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
                    $organization_address->full_address = (isset($organizationDetails->address_one) && isset($organizationDetails->address_one)) ? $organizationDetails->address_one.', '.$organizationDetails->address_two : null;
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
                        $custom_url = Str::contains($organization->vanity_link, '/org/') ? Str::after($organization->vanity_link, '/org/') : $organization->vanity_link;

                        $oldOrganizationCustomizations = new OrganizationCustomization();
                        $oldOrganizationCustomizations->organization_id = $newOrganization->id;
                        $oldOrganizationCustomizations->enable_custom_login_and_registration = $enable_custom_login_and_registration;
                        $oldOrganizationCustomizations->use_main_org_logo = $use_main_org_logo;
                        $oldOrganizationCustomizations->custom_logo_image = $organizationCustomizations->custom_logo_image;
                        $oldOrganizationCustomizations->custom_hero_image = $organizationCustomizations->custom_hero_image;
                        $oldOrganizationCustomizations->custom_background_color = $organizationCustomizations->custom_background_color;
                        $oldOrganizationCustomizations->custom_url = $custom_url;
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

            foreach ($organizations as $organization) {
                $this->migrateOrganization($organization);
            }

            DB::commit();
            $this->info('Migration completed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error during migration: '.$e->getMessage());
        }
    }

    private function migrateOrganization($organization)
    {
        $user = User::find($organization->user_id);
        if (!$user) {
            return; // Skip if user does not exist
        }

        $organizationDetails = DB::connection('mysql2')->table('organisations_details')
            ->where('organisations_id', $organization->id)->first();
        $organizationCustomizations = DB::connection('mysql2')->table('organization_customizations')
            ->where('organization_id', $organization->id)->first();
        $organizationPeoples = DB::connection('mysql2')->table('peoples')
            ->where('organisation', $organization->id)->get();

        $newOrganization = Organization::find($organization->id) ?? new Organization();
        $category = $this->getCategory($organization->category);

        $newOrganization->fill([
            'id'                           => $organization->id,
            'uuid'                         => Randomize::chars(10)->alphanumeric()->unique()->generate(),
            'language'                     => $organization->language,
            'user_id'                      => $organization->user_id,
            'title'                        => $organization->name,
            'display_name'                 => $organization->name,
            'description'                  => $organization->description ?? null,
            'slug'                         => $organization->slug,
            'cover_image'                  => $organization->cover_image ?? config('site-settings.default_organization_cover_image'),
            'profile_image'                => $organization->profile_image ?? config('site-settings.default_organization_profile_image'),
            'custom_url'                   => $organization->vanity_slug ?? null,
            'website'                      => $organization->website ?? null,
            'about'                        => $organization->about ?? null,
            'category'                     => $category,
            'status'                       => $this->getStatus($organization->status),
            'total_employees'              => $organizationDetails->number_employees ?? 0,
            'is_verified'                  => $organization->is_verified,
            'business_challenge_tacklings' => $this->getBusinessChallengeOption($organization->bussiness_challenge_option),
        ]);
        $newOrganization->save();

        $user->attachRole('organization_owner', $newOrganization->id);
        $this->migrateAddress($organization, $organizationDetails, $newOrganization);
        $this->migrateCustomizations($organizationCustomizations, $newOrganization);
        $this->migrateMembers($organizationPeoples, $newOrganization);
    }

    private function getCategory($oldCategoryId)
    {
        if (empty($oldCategoryId) || $oldCategoryId == '0') {
            return null;
        }

        $oldCategory = DB::connection('mysql2')->table('categories')->find($oldCategoryId);
        if ($oldCategory) {
            $category = Category::where('title', $oldCategory->name)->first();

            return $category ? $category->id : null;
        }

        return null;
    }

    private function getBusinessChallengeOption($option)
    {
        $optionsMap = [
            'sales_marketing'                             => '1',
            'human_resources'                             => '2',
            'it_management'                               => '3',
            'customer_service'                            => '4',
            'research_development'                        => '5',
            'business_evelopment'                         => '6',
            'sustainability_and_environmental_management' => '7',
        ];

        return $optionsMap[$option] ?? null;
    }

    private function getStatus($status)
    {
        return match ($status) {
            '0'     => '0',
            '1'     => '1',
            default => '3',
        };
    }

    private function migrateAddress($organization, $organizationDetails, $newOrganization)
    {
        $address = OrganizationAddress::where('organization_id', $newOrganization->id)->first() ?? new OrganizationAddress();
        $address->fill([
            'organization_id' => $newOrganization->id,
            'latitude'        => $organization->latitude ?? null,
            'longitude'       => $organization->longitude ?? null,
            'full_address'    => $organizationDetails->address_one.', '.$organizationDetails->address_two ?? null,
            'address_1'       => $organizationDetails->address_one ?? null,
            'address_2'       => $organizationDetails->address_two ?? null,
            'city'            => $organizationDetails->city ?? null,
            'state'           => $organizationDetails->province ?? null,
            'country'         => $organizationDetails->country ?? null,
            'zip_code'        => $organizationDetails->postal_code ?? null,
        ]);
        $address->save();
    }

    private function migrateCustomizations($organizationCustomizations, $newOrganization)
    {
        if (!$organizationCustomizations) {
            return;
        }

        $customization = new OrganizationCustomization();
        $customization->fill([
            'organization_id'                      => $newOrganization->id,
            'enable_custom_login_and_registration' => $organizationCustomizations->enable_custom_login_and_registration == '1' ? '1' : '0',
            'use_main_org_logo'                    => $organizationCustomizations->use_main_org_logo == '1' ? '1' : '0',
            'custom_logo_image'                    => $organizationCustomizations->custom_logo_image,
            'custom_hero_image'                    => $organizationCustomizations->custom_hero_image,
            'custom_background_color'              => $organizationCustomizations->custom_background_color,
        ]);
        $customization->save();
    }

    private function migrateMembers($organizationPeoples, $newOrganization)
    {
        if ($organizationPeoples->isEmpty()) {
            return;
        }

        OrganizationMember::where('organization_id', $newOrganization->id)->delete();

        foreach ($organizationPeoples as $people) {
            $member = new OrganizationMember();
            $member->fill([
                'organization_id' => $newOrganization->id,
                'name'            => $people->name,
                'position'        => $people->description,
                'image'           => $people->image,
            ]);
            $member->save();
        }
    }
}
