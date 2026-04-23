<?php

namespace App\Services;

use App\Models\Categories;

class CategoryServices {
    public function getAllCategory() {
        return Categories::all();
    }

    public function createCategory(array $data)
    {
        return Categories::create($data);
    }

    public function getCategoryById(int $id): ?Categories
    {
        return Categories::find($id);
    }

    public function updateCategory(Categories $category, array $data): Categories
    {
        $category->update($data);

        return $category->fresh();
    }
}
