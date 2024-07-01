<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationTypeMode;
use Exception;
use Illuminate\Support\Facades\DB;

class OrganizationTypeModeService
{
    public function storeOrganizationType($organizationId, $request)
    {
        try {
            DB::beginTransaction();
            $deleteOrganizationTypeMode = OrganizationTypeMode::where('organization_id', $organizationId)->delete();
            foreach ($request->type as $organizationType) {
                switch ($organizationType) {
                    case 'assess':
                        $type = '0';
                        $value = '0';
                        break;
                    case 'onboard':
                        $type = '0';
                        $value = '1';
                        break;
                    case 'engage':
                        $type = '0';
                        $value = '2';
                        break;
                    case 'grow':
                        $type = '0';
                        $value = '3';
                        break;
                }

                $organizationTypeMode = new OrganizationTypeMode();
                $organizationTypeMode->organization_id = $organizationId;
                $organizationTypeMode->type_mode = $type;
                $organizationTypeMode->value = $value;
                $organizationTypeMode->save();
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }
}
