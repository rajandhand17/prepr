<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleTypeModes;
use function Symfony\Component\Translation\t;

class ResourceModuleTypeModesService
{
    const TYPE = 'type';
    const VALUE = 'value';
    // Defining all values in single array
    private $mappings = [
        'assess'     => [self::TYPE => '0', self::VALUE => '0'],
        'onboard'    => [self::TYPE => '0', self::VALUE => '1'],
        'engage'     => [self::TYPE => '0', self::VALUE => '2'],
        'grow'       => [self::TYPE => '0', self::VALUE => '3'],
        'team'       => [self::TYPE => '1', self::VALUE => '4'],
        'individual' => [self::TYPE => '1', self::VALUE => '5'],
    ];

    // Base on key store data
    public function createResourceModuleTypeModes($request, $resourceModuleId)
    {
        try {
            ResourceModuleTypeModes::where('resource_module_id', $resourceModuleId)->delete();

            foreach (['type', 'mode'] as $key) {
                if ($request->has($key)) {
                    $this->createEntries($request->$key, $resourceModuleId);
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function createEntries($items, $resourceModuleId)
    {
        try {
            foreach ($items as $item) {
                if (isset($this->mappings[$item])) {
                    ResourceModuleTypeModes::create([
                        'resource_module_id' => $resourceModuleId,
                        'type_mode'          => $this->mappings[$item][self::TYPE],
                        'value'              => $this->mappings[$item][self::VALUE],
                    ]);
                }
            }
        }catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }

    }

    public static function getResourceModuleBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
           return ResourceModuleTypeModes::where(['type_mode'=>'0','value'=>$type])->get();
        }catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
