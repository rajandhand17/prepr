<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTypeMode;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ChallengeTypeModeService
{
    public function storeChallengeTypeMode($request, $challengeId)
    {
        try {
            $typeMappings = [
                'assess'  => ['type' => '0', 'value' => '0'],
                'onboard' => ['type' => '0', 'value' => '1'],
                'engage'  => ['type' => '0', 'value' => '2'],
                'grow'    => ['type' => '0', 'value' => '3'],
            ];

            $modeMappings = [
                'team'       => ['type' => '1', 'value' => '4'],
                'individual' => ['type' => '1', 'value' => '5'],
            ];

            $deleteChallengeTypeMode = ChallengeTypeMode::where('challenge_id', $challengeId)->delete();
            // Create new challenge type modes based on request types
            if ($request->has('type')) {
                foreach ($request->type as $challengeType) {
                    if (isset($typeMappings[$challengeType])) {
                        ChallengeTypeMode::create([
                            'challenge_id' => $challengeId,
                            'type_mode'    => $typeMappings[$challengeType]['type'],
                            'value'        => $typeMappings[$challengeType]['value'],
                        ]);
                    }
                }
            }

            // Create new challenge type modes based on request modes
            if ($request->has('mode')) {
                foreach ($request->mode as $challengeMode) {
                    if (isset($modeMappings[$challengeMode])) {
                        ChallengeTypeMode::create([
                            'challenge_id' => $challengeId,
                            'type_mode'    => $modeMappings[$challengeMode]['type'],
                            'value'        => $modeMappings[$challengeMode]['value'],
                        ]);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeType($challengeType)
    {
        try {
            $assessChallengeIds = collect([]);
            $onboardChallengeIds = collect([]);
            $engageChallengeIds = collect([]);
            $growChallengeIds = collect([]);
            if (in_array('assess', $challengeType)) {
                $assessChallengeIds = collect(config('constants.resource_types.assess'));
            }
            if (in_array('onboard', $challengeType)) {
                $onboardChallengeIds = collect(config('constants.resource_types.onboard'));
            }
            if (in_array('engage', $challengeType)) {
                $engageChallengeIds = collect(config('constants.resource_types.engage'));
            }
            if (in_array('grow', $challengeType)) {
                $growChallengeIds = collect(config('constants.resource_types.grow'));
            }
            $challengeCollection = new Collection();
            $challengeCollection = $challengeCollection->concat($assessChallengeIds);
            $challengeCollection = $challengeCollection->concat($onboardChallengeIds);
            $challengeCollection = $challengeCollection->concat($engageChallengeIds);
            $challengeCollection = $challengeCollection->concat($growChallengeIds);

            return ChallengeTypeMode::where(['type_mode' => '0'])->whereIn('value', $challengeCollection)->pluck('challenge_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
