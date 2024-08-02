<?php

namespace App\Traits\Maestro\Tag;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\TagGroupService;
use Exception;

trait TagGroupTrait
{
    private function createTagGroup($request)
    {
        try {
            if (TagGroupService::createTagGroup($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getTagGroupById($id)
    {
        try {
            return TagGroupService::getTagGroupById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function updateTagGroupById($id, $request)
    {
        try {
            if (TagGroupService::updateTagGroupById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteTagGroupById($id)
    {
        try {
            if (TagGroupService::deleteTagGroupById($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getTagGroups()
    {
        try {
            $tagGroups = TagGroupService::getTagGroups();
            if ($tagGroups) {
                return $tagGroups;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
