<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Services\CategoryServices;

class CategoryManagementController extends Controller
{
    public function store(StoreCategoryRequest $request, CategoryServices $categoryServices)
    {
        $category = $categoryServices->createCategory($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
            ],
            'message' => 'Category created successfully'
        ], 201);
    }

    public function update(int $id, UpdateCategoryRequest $request, CategoryServices $categoryServices)
    {
        $category = $categoryServices->getCategoryById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Category not found'
            ], 404);
        }

        $updatedCategory = $categoryServices->updateCategory($category, $request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $updatedCategory,
            ],
            'message' => 'Category updated successfully'
        ], 200);
    }
}
