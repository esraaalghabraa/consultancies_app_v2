<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;

class CategoryService
{
    use ResponseTrait,ImageTrait;

    public function createCategory($data)
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
       return Category::create($processedData);
    }

    public function updateCategory($data)
    {
        $processedData = [];

        $category = Category::find($data['id']);

        foreach ($data as $key => $value) {
            if ($key == 'image' && $value !== null) {
                if ($category->image) {
                    $this->deleteImageH($category->image);
                }
                // Process the image if it is set and not null
                $processedData[$key] = $this->setImage($value);
            } else {
                $processedData[$key] = $value;
            }
        }
        $category->update($processedData);
        $category->save();

    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category->image) {
            $this->deleteImageH($category->image);
        }
        if ($category->experts_number > 0) {
            throw new \Exception('You cannot delete this category because there are experts dependent on it');
        }

        if ($category->sub_categories_number > 0) {
            throw new \Exception('You cannot delete this category because there are subcategories dependent on it');
        }
        $category->delete();
    }

    public function getCategories($filter = [])
    {
        $categories = Category::query()->with('experts');

        if (isset($filter['name'])) {
            $categories->where('name', 'like', '%' . $filter['name'] . '%');
        }
        return $categories->get();
    }

    public function changeActive($data)
    {
        $category = Category::find($data['id']);
        $category->update([
            'active' => !$category->active
        ]);
        $category->save();
    }

}
