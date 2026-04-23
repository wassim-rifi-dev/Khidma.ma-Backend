<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
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
}
