<?php

namespace App\Traits\Maestro\Tag;

use App\Services\Maestro\Tag\TagService;
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
            dd($e);

            return false;
        }
    }

    private function getTagById($id)
    {
        try {
            return TagService::getTagById($id);
        } catch (Exception $e) {
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
            return false;
        }
    }

    private function getTags()
    {
        try {
            $skills = TagService::getTags();
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
