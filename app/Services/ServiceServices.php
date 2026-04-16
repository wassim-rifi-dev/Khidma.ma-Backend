<?php

namespace App\Services;

use App\Models\Service;


class ServiceServices {
    public function createServices(array $data) {
        return Service::create($data);
    }
}
