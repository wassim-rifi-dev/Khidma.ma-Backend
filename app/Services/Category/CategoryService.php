<?php

namespace App\Services\Category;

use App\Models\Category;

class CategoryService {
    public function getAllCategory() {
        return Category::withCount(['services', 'professionals'])
            ->latest()
            ->get();
    }

    public function createCategory(array $data)
    {
        return Category::create($data)->loadCount(['services', 'professionals']);
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::withCount(['services', 'professionals'])->find($id);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh()->loadCount(['services', 'professionals']);
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
