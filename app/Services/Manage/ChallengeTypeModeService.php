<?php

namespace App\Services\Manage;

use App\Models\ChallengeTypeMode;
use Exception;

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
}
