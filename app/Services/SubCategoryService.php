<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;

class SubCategoryService
{
    use ResponseTrait,ImageTrait;

    public function createSubCategory($data)
    {
        $processedData = [];

        foreach ($data as $key => $value) {
            if ($key == 'image' && $value !== null) {
                // Process the image if it is set and not null
                $processedData[$key] = $this->setImage($value);
            } else {
                $processedData[$key] = $value;
            }
        }

        // Create the category with all the processed data
        return SubCategory::create($processedData);
    }

    public function updateSubCategory($data)
    {
        $processedData = [];

        $sub_category = SubCategory::find($data['id']);

        foreach ($data as $key => $value) {
            if ($key == 'image' && $value !== null) {
                if ($sub_category->image) {
                    $this->deleteImageH($sub_category->image);
                }
                // Process the image if it is set and not null
                $processedData[$key] = $this->setImage($value);
            } else {
                $processedData[$key] = $value;
            }
        }
        $sub_category->update($processedData);
        $sub_category->save();

    }

    public function deleteSubCategory($id)
    {
        $sub_category = SubCategory::find($id);
        if ($sub_category->image) {
            $this->deleteImageH($sub_category->image);
        }
        if ($sub_category->experts_number > 0) {
            throw new \Exception('You cannot delete this category because there are experts dependent on it');
        }
        $sub_category->delete();
    }

    public function getSubCategories($filter = [])
    {
        $sub_categories = SubCategory::query();

        if (isset($filter['name'])) {
            $sub_categories->where('name', 'like', '%' . $filter['name'] . '%');
        }
        return $sub_categories->get();
    }

    public function changeActive($data)
    {
        $sub_category = SubCategory::find($data['id']);
        $sub_category->update([
            'active' => !$sub_category->active
        ]);
        $sub_category->save();
    }
}
