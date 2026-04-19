<?php

namespace App\Services;

use App\Models\Categories;

class CategoryServices {
    public function getAllCategory() {
        return Categories::all();
    }
}
