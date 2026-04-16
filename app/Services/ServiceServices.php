<?php

namespace App\Services;

use App\Models\Service;


class ServiceServices {
    public function createServices(array $data) {
        return Service::create($data);
    }

    public function getAllServices() {
        return Service::all();
    }

    public function getServiceById(int $id) {
        return Service::find($id);
    }

    public function updateService(Service $service, array $data) {
        $service->update($data);

        return $service->fresh();
    }

    public function deleteService(Service $service) {
        return $service->delete();
    }
}
