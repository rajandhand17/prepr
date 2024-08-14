<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionTypeModes;

class ResourceCollectionTypeModesService
{
    public static function getResourceCollectionBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceCollectionTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionType($resourceCollectionId)
    {
        try {
            return ResourceCollectionTypeModes::where([
                'type_mode'              => config('constants.resource_mode.type'),
                'resource_collection_id' => $resourceCollectionId])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceCollectionMode($resourceCollectionId)
    {
        try {
            return ResourceCollectionTypeModes::where([
                'type_mode'              => config('constants.resource_mode.mode'),
                'resource_collection_id' => $resourceCollectionId])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // Storing type's and mode's
    public function createUpdateResourceCollectionTypeModes($request, $resourceCollectionId)
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

            // Delete existing entries for the given resource collection
            ResourceCollectionTypeModes::where('resource_collection_id', $resourceCollectionId)->delete();
            // Helper function to create resource collection type modes
            $createResourceCollectionTypeMode = function ($mappings, $items) use ($resourceCollectionId) {
                foreach ($items as $item) {
                    if (isset($mappings[$item])) {
                        ResourceCollectionTypeModes::create([
                            'resource_collection_id' => $resourceCollectionId,
                            'type_mode'              => $mappings[$item]['type'],
                            'value'                  => $mappings[$item]['value'],
                        ]);
                    }
                }
            };

            // Create new resource collection type modes based on request types and modes
            if ($request->has('type')) {
                $createResourceCollectionTypeMode($typeMappings, $request->type);
            }

            if ($request->has('mode')) {
                $createResourceCollectionTypeMode($modeMappings, $request->mode);
            }

            return true;
        } catch (\Exception $e) {
            // Log the exception or handle it according to your needs
            Log::error('Failed to store challenge type modes: '.$e->getMessage());

            return false;
        }
    }

    public static function cloneResourceCollectionTypeModes($originalResourceCollectionAssociation, $clonedResourceCollectionId)
    {
        try {
            if ($originalResourceCollectionAssociation) {
                $cloneResourceModuleSKills = $originalResourceCollectionAssociation->replicate();
                $cloneResourceModuleSKills->resource_collection_id = $clonedResourceCollectionId;
                $cloneResourceModuleSKills->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
