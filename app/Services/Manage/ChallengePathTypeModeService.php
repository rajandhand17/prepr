<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathsTypeMode;
use App\Models\ResourceCollectionTypeModes;
use Exception;

class ChallengePathTypeModeService
{
    // Storing type's and mode's
    public function createUpdateChallengePathTypeModels($request, $challengePathId)
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

            // Helper function to create challenge path type modes
            $challengePathsTypeMode = function ($mappings, $items) use ($challengePathId) {
                foreach ($items as $item) {
                    if (isset($mappings[$item])) {
                        ChallengePathsTypeMode::create([
                            'challenge_path_id'      => $challengePathId,
                            'type_mode'              => $mappings[$item]['type'],
                            'value'                  => $mappings[$item]['value'],
                        ]);
                    }
                }
            };

            // Create new challenge path type modes based on request types and modes
            if ($request->has('type')) {
                ChallengePathsTypeMode::where('challenge_path_id', $challengePathId)->where('type_mode', '0')->delete();
                $challengePathsTypeMode($typeMappings, $request->type);
            }

            if ($request->has('mode')) {
                ChallengePathsTypeMode::where('challenge_path_id', $challengePathId)->where('type_mode', '1')->delete();
                $challengePathsTypeMode($modeMappings, $request->mode);
            }

            return true;
        } catch (\Exception $e) {
            // Log the exception or handle it according to your needs
            UtilityHelper::logError($e);
            return false;
        }
    }
}
