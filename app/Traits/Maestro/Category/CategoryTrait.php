<?php

namespace App\Traits\Maestro\Category;

use App\Services\Maestro\CategoryService;
use App\Helpers\UtilityHelper;
use Exception;

trait CategoryTrait
{
    private function getCategories()
    {
        try {
            $categories = CategoryService::getCategories();
            if ($categories) {
                return $categories;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getCategoryById($id)
    {
        try {
            $category = CategoryService::getCategoryById($id);
            if ($category) {
                return $category;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getComponentsById($category)
    {
        try {
            $components = CategoryService::getComponentsById($category);
            if ($components) {
                return $components;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getFirstCategoryById($id)
    {
        try {
            $category = CategoryService::getFirstCategoryById($id);
            if ($category) {
                return $category;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function storeUpdateCategory($request, $id, $moduleMode)
    {
        try {
            if (CategoryService::storeUpdateCategory($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function findCategory($id)
    {
        try {
            $category = CategoryService::findCategory($id);
            if ($category) {
                return $category;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function deleteCategory($category)
    {
        try {
            if (CategoryService::deleteCategory($category)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getSubCategoryById($id)
    {
        try {
            $subCategory = CategoryService::getSubCategoryById($id);
            if ($subCategory) {
                return $subCategory;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getCategory()
    {
        try {
            $category = CategoryService::getCategory();
            if ($category) {
                return $category;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
