<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Organization as Organizations;
use App\Models\OrganizationAddress;
use App\Models\OrganizationSocialLink;
use App\Models\SocialLink;
use DB;
use Illuminate\Console\Command;

class Organization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:organization';

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
                foreach ($organizations as $key => $single_organization) {
                    $organization_details = [
                        'language'      => $single_organization->language,
                        'user_id'       => $single_organization->user_id,
                        'name'          => $single_organization->name,
                        'slug'          => $single_organization->slug,
                        'vanity_slug'   => $single_organization->vanity_slug,
                        'description'   => $single_organization->description,
                        'cover_image'   => $single_organization->cover_image,
                        'profile_image' => $single_organization->profile_image,
                        'website'       => $single_organization->website,
                    ];

                    $check_organization = Organizations::where(['name'=>$single_organization->name])->first();
                    if (!$check_organization) {
                        $organization_create = Organizations::create($organization_details);
                        if (isset($organization_create->id)) {
                            $organisations_details = DB::connection('mysql2')->table('organisations_details')->get();
                            foreach ($organisations_details as $key => $detailed) {
                                $organization_address = [
                                    'organization_id'=> $organization_create->id,
                                    'latitude'       => $single_organization->latitude,
                                    'longitude'      => $single_organization->longitude,
                                    'address'        => $single_organization->address,
                                    'city'           => $detailed->city,
                                    'state'          => $detailed->province,
                                    'country'        => $detailed->country,
                                    'zip_code'       => $detailed->postal_code,
                                ];
                                $organization_address_create = OrganizationAddress::create($organization_address);
                            }
                            if ($single_organization->facebook !== null) {
                                $getid = SocialLink::where('name', 'Facebook')->first();

                                $social_data = [
                                    'organization_id'  => $organization_create->id,
                                    'social_media_link'=> $single_organization->facebook,
                                    'social_link_id'   => $getid->id,
                                ];
                                $organizationSocialLink = OrganizationSocialLink::create($social_data);
                            }
                            if ($single_organization->linked !== null) {
                                $getid = SocialLink::select('id')->where('name', 'LinkedIn')->first();
                                $social_linked_data = [
                                    'organization_id'  => $organization_create->id,
                                    'social_media_link'=> $single_organization->linked,
                                    'social_link_id'   => $getid->id,
                                ];
                                $organizationSocialLink = OrganizationSocialLink::create($social_linked_data);
                            }
                            if ($single_organization->twitter !== null) {
                                $getid = SocialLink::select('id')->where('name', 'Twitter')->first();
                                $social_twitter_data = [
                                    'organization_id'  => $organization_create->id,
                                    'social_media_link'=> $single_organization->twitter,
                                    'social_link_id'   => $getid->id,
                                ];
                                $organizationSocialLink = OrganizationSocialLink::create($social_twitter_data);
                            }
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
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
