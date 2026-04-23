<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Services\CategoryServices;

class CategoryManagementController extends Controller
{
    public function index(CategoryServices $categoryServices)
    {
        $categories = $categoryServices->getAllCategory();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'total' => $categories->count(),
            ],
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

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

    public function destroy(int $id, CategoryServices $categoryServices)
    {
        $category = $categoryServices->getCategoryById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Category not found'
            ], 404);
        }

        $categoryServices->deleteCategory($category);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
