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
}
