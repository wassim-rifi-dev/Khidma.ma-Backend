<?php

namespace App\Http\Controllers;

use App\Services\Category\CategoryService;

class CategoryController extends Controller
{
    public function index(CategoryService $categoryService) {
        $categories = $categoryService->getAllCategory();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories
            ],
            'message' => 'This is categories.'
        ], 200);
    }
}
