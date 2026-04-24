<?php

namespace App\Services;

use App\Models\Categories;

class CategoryServices {
    public function getAllCategory() {
        return Categories::withCount(['services', 'professionals'])
            ->latest()
            ->get();
    }

    public function createCategory(array $data)
    {
        return Categories::create($data)->loadCount(['services', 'professionals']);
    }

    public function getCategoryById(int $id): ?Categories
    {
        return Categories::withCount(['services', 'professionals'])->find($id);
    }

    public function updateCategory(Categories $category, array $data): Categories
    {
        $category->update($data);

        return $category->fresh()->loadCount(['services', 'professionals']);
    }

    public function deleteCategory(Categories $category): void
    {
        $category->delete();
    }
}
