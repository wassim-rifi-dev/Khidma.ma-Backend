<?php

namespace App\Http\Controllers;

use App\Services\CategoryServices;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryServices $categoryServices) {
        $categories = $categoryServices->getAllCategory();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories
            ],
            'message' => 'This is categories.'
        ], 200);
    }
}
