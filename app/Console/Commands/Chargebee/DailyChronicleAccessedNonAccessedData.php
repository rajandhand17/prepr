<?php

namespace App\Console\Commands\Chargebee;

use App\Helpers\ChargebeeHelper;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\LabProgram;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Subscription;
use Countable;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyChronicleAccessedNonAccessedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chargebee-subscription:daily-chronicle-accessed-non-accessed-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to fetch subscription details running at every minutes. Also it updates the plan and limits in local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Command Initiated to fetch Subscriptions');
            DB::beginTransaction();

            // Calculate timestamps for the past minute and the current time
            $oneMinuteAgo = time() - 60;
            $currentTimestamp = time();
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $allSubscriptions = Subscription::all([
                'updated_at[after]'  => $oneMinuteAgo, // Subscriptions updated after the past minute
                'updated_at[before]' => $currentTimestamp, // Subscriptions updated before the current time
                'sort_by[desc]'      => 'updated_at', // Sort by updated_at field in descending order
            ]);
            $organizationIds = [];
            foreach ($allSubscriptions as $subscription) {
                $organizationIds[] = $subscription->subscription()->cfOrgId;
            }

            if (!empty($organizationIds)) {
                $components = ['labs', 'preBuiltLabs', 'labPrograms', 'challenges', 'challengePaths', 'resourceModules', 'resourceCollections', 'resourceGroups', 'managerInvites', 'userInvites'];
                foreach ($components as $component) {
                    $componentIds = [];

                    foreach ($organizationIds as $organizationId) {
                        if ($organizationId) {
                            $planDetails = ChargebeeHelper::getSubscribedPlanDetailForOrganization($organizationId);
                            if (!empty($planDetails) && $planDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId != config('chargebee.chargebee_plan.unlimited_plan')) {
                                $totalLimit = ChargebeeHelper::getTotalLimits($organizationId, $component);
                                $createdComponentIds = ChargebeeHelper::getComponentUsage($organizationId, $component);

                                if (is_array($createdComponentIds) || $createdComponentIds instanceof Countable) {
                                    if (!empty($createdComponentIds) && count($createdComponentIds) > 0 && $totalLimit != []) {
                                        foreach ($createdComponentIds as $key => $createdComponentId) {
                                            if ($key < $totalLimit) {
                                                $componentIds['accessed'][] = $createdComponentId;
                                            } else {
                                                $componentIds['nonAccessed'][] = $createdComponentId;
                                            }
                                        }
                                    }
                                }
                            } else {
                                // Update  component with organization set as unlimited
                                $lab = Lab::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $labProgram = LabProgram::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $challenge = Challenge::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $challengePath = ChallengePath::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $resourceModule = ResourceModule::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $resourceCollection = ResourceCollection::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                                $resourceGroup = ResourceGroup::where('organization_id', $organizationId)->update(['is_accessible' => '1']);
                            }
                            // Feeding into local database with the current pack status and feature
                            $feedChargebeePlanDetails = ChargebeeHelper::createChargebeePlanDetails($organizationId);
                        }
                    }

                    // disabling the data fetch from above accessed and non-accessed data.
                    if ($component == 'labs') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            Lab::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            Lab::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'preBuiltLabs') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            Lab::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                            $associatedChallenges = ComponentAssociation::whereIn('lab_id', $componentIds['nonAccessed'])->whereNotNull('challenge_id')->pluck('challenge_id');
                            $associatedChallengePaths = ComponentAssociation::whereIn('lab_id', $componentIds['nonAccessed'])->whereNotNull('challenge_path_id')->pluck('challenge_path_id');
                            Challenge::whereIn('id', $associatedChallenges)->update(['is_accessible' => '0']);
                            ChallengePath::whereIn('id', $associatedChallengePaths)->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            Lab::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                            $associatedChallenges = ComponentAssociation::whereIn('lab_id', $componentIds['accessed'])->whereNotNull('challenge_id')->pluck('challenge_id');
                            $associatedChallengePaths = ComponentAssociation::whereIn('lab_id', $componentIds['accessed'])->whereNotNull('challenge_path_id')->pluck('challenge_path_id');
                            Challenge::whereIn('id', $associatedChallenges)->update(['is_accessible' => '1']);
                            ChallengePath::whereIn('id', $associatedChallengePaths)->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'labPrograms') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            LabProgram::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            LabProgram::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'challenges') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            Challenge::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            Challenge::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'challengePaths') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            ChallengePath::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            ChallengePath::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'resourceModules') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            ResourceModule::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            ResourceModule::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'resourceCollections') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            ResourceCollection::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            ResourceCollection::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    } elseif ($component == 'resourceGroups') {
                        if (!empty($componentIds) && array_key_exists('nonAccessed', $componentIds)) {
                            ResourceGroup::whereIn('id', $componentIds['nonAccessed'])->update(['is_accessible' => '0']);
                        }
                        if (!empty($componentIds) && array_key_exists('accessed', $componentIds)) {
                            ResourceGroup::whereIn('id', $componentIds['accessed'])->update(['is_accessible' => '1']);
                        }
                    }
                }
            }
            DB::commit();
            $this->info('Command completed successfully');

            return 0;
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}
