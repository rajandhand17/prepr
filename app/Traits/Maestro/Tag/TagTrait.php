<?php

namespace App\Traits\Maestro\Tag;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\TagService;
use Exception;

trait TagTrait
{
    private function createTag($request)
    {
        try {
            if (TagService::createTag($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getTagById($id)
    {
        try {
            return TagService::getTagById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function updateTagById($id, $request)
    {
        try {
            if (TagService::updateTagById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteTagById($id)
    {
        try {
            if (TagService::deleteTag($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getTags()
    {
        try {
            $tags = TagService::getTags();
            if ($tags) {
                return $tags;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
