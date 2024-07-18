<?php

namespace App\Services\Maestro\Category;

use App\Models\Category;
use App\Models\Language;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public static function getLanguage()
    {
        try {
            $language = Language::where('status', 1)->get();
            if ($language != null) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

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
            DB::beginTransaction();
            $componentString = '';
            $componentsArray = $request->get('components');
            if (is_array($componentsArray) && count($componentsArray) > 0) {
                $componentString = implode(',', $componentsArray);
            }
            $languages = Language::where('status', 1)->get();
            if (!empty($id)) {
                $category = Category::find($id);
            } else {
                $category = new Category();
            }

            if (!empty($languages)) {
                foreach ($languages as $single) {
                    if ($single->iso == 'en') {
                        $columName = 'title';
                    } else {
                        $columName = $single->iso;
                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                            $columName = str_replace(' ', '_', $columName);
                        }
                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                            $columName = str_replace('-', '_', $columName);
                        }
                        $columName = $columName.'_title';
                    }
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
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            DB::rollback();

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
}
