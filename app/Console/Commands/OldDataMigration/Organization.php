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

            if ($organizations->isEmpty()) {
                $this->error('No organizations found.');

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
