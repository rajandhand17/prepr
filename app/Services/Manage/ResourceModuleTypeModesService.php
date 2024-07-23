<?php

namespace App\Services\Manage;

use App\Models\ResourceModuleTypeModes;

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
            ResourceModuleTypeModes::where("resource_module_id", $resourceModuleId)->delete();

            foreach (['type', 'mode'] as $key) {
                if ($request->has($key)) {
                    $this->createEntries($request->$key, $resourceModuleId);
                }
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to create resource module type modes: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating resource module type modes.'], 500);
        }
    }

    private function createEntries($items, $resourceModuleId)
    {
        foreach ($items as $item) {
            if (isset($this->mappings[$item])) {
                ResourceModuleTypeModes::create([
                    'resource_module_id' => $resourceModuleId,
                    'type_mode'          => $this->mappings[$item][self::TYPE],
                    'value'              => $this->mappings[$item][self::VALUE],
                ]);
            }
        }
    }

}
