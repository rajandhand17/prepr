<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\Category;
use Exception;

class CategoryService
{
    public static function getCategories()
    {
        try {
            $categories = Category::where(['parent_id' => '0'])->latest()->pluck('title', 'id')->prepend('Select Category', '');
            if ($categories != null) {
                return $categories;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCategoryById($id)
    {
        try {
            $category = Category::find($id);
            if ($category != null) {
                return $category;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getComponentsById($category)
    {
        try {
            $allComponents = ['lab' => 'lab', 'challenge' => 'challenge', 'project' => 'project'];
            $components = [];
            $existedComponents = explode(',', $category->components);
            foreach ($allComponents as $key => $component) {
                if (in_array($component, $existedComponents)) {
                    $components[$key] = $component;
                } else {
                    $components[$key] = '';
                }
            }

            return $components;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getFirstCategoryById($id)
    {
        try {
            return Category::where('id', $id)->where('parent_id', '0')->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateCategory($request, $id, $moduleMode)
    {
        try {
            $componentString = '';
            $componentsArray = $request->get('components');
            if (is_array($componentsArray) && count($componentsArray) > 0) {
                $componentString = implode(',', $componentsArray);
            }
            if (!empty($id)) {
                $category = Category::find($id);
            } else {
                $category = new Category();
            }
            $languages = LanguageService::getAllActiveLanguages();
            if (!empty($languages)) {
                foreach ($languages as $single) {
                    $columName = UtilityHelper::getColumName($single->iso, 'title');
                    $category->$columName = $request->$columName;
                }
            }
            if ($request->filled('parent_id')) {
                $category->parent_id = $request->get('parent_id');
            } else {
                $category->parent_id = '0';
            }

            $category->components = $componentString;
            if ($category->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findCategory($id)
    {
        try {
            return Category::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteCategory($category)
    {
        try {
            return $category->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getSubCategoryById($id)
    {
        try {
            return Category::where('parent_id', $id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCategory()
    {
        try {
            return Category::where(['parent_id' => '0'])->orderBy('id', 'DESC');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCategoryCount($parent_id)
    {
        try {
            return Category::where('parent_id', $parent_id)->count();
        } catch (Exception $e) {
            return false;
        }
    }
}
