<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\Category\CategoryService;

class CategoryController extends Controller
{
    public function index(CategoryService $categoryService)
    {
        $categories = $categoryService->getAllCategory();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'total' => $categories->count(),
            ],
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

    public function store(StoreCategoryRequest $request, CategoryService $categoryService)
    {
        $category = $categoryService->createCategory($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
            ],
            'message' => 'Category created successfully'
        ], 201);
    }

    public function update(int $id, UpdateCategoryRequest $request, CategoryService $categoryService)
    {
        $category = $categoryService->getCategoryById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Category not found'
            ], 404);
        }

        $updatedCategory = $categoryService->updateCategory($category, $request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $updatedCategory,
            ],
            'message' => 'Category updated successfully'
        ], 200);
    }

    public function destroy(int $id, CategoryService $categoryService)
    {
        $category = $categoryService->getCategoryById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Category not found'
            ], 404);
        }

        $categoryService->deleteCategory($category);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
