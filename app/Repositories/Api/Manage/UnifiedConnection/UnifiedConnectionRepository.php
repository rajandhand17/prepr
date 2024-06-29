<?php

namespace App\Repositories\Api\Manage\UnifiedConnection;

use App\Helpers\Unified\UnifiedHelper;
use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Services\Manage\UnifiedConnectionService;
use Illuminate\Support\Facades\DB;

class UnifiedConnectionRepository implements UnifiedConnectionInterface
{
    /**
     * @param UnifiedConnectionService $unifiedConnectionService
     */
    public function __construct(protected UnifiedConnectionService $unifiedConnectionService)
    {
    }

    /**
     * @param      $data
     * @param User $user
     *
     * @return array|false
     */
    public function getIntegrations($data, User $user): array|false
    {
        try {
            return $this->unifiedConnectionService->getIntegrations($data, $user);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function listEmployee($connectionId, $stateData)
    {
        try {
            $data = DB::transaction(function () use ($connectionId, $stateData) {
                $listEmployee = UnifiedHelper::getEmployee($connectionId);
                $createConnection = true;
                if (!config('unified.use_faker')) {
                    $createConnection = $this->unifiedConnectionService->createConnection($connectionId, $stateData);
                }

                return [
                    'listEmployee'     => $listEmployee,
                    'createConnection' => $createConnection,
                ];
            });
            if ($data['listEmployee'] && $data['createConnection']) {
                DB::commit();

                return $data['listEmployee'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            DB::rollBack();

            return false;
        }
    }

    /**
     * @param $data
     *
     * @return false|array
     */
    public function inviteMembers($data): false|array
    {
        try {
            return $this->unifiedConnectionService->inviteMembers($data);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
