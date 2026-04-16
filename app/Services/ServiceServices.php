<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\Service;


class ServiceServices {
    public function createServices(array $data) {
        return Service::create($data);
    }

    public function getCategoryByName(string $name) {
        return Categories::where('name', $name)->first();
    }
}
