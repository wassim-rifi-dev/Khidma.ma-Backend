<?php

namespace App\Services;

use App\Models\Service;


class ServiceServices {
    public function createServices(array $data) {
        return Service::create($data);
    }

    public function getAllServices(int $perPage = 10) {
        return Service::with(['category', 'professional.user'])->paginate($perPage);
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

    public function getDeletedServicesByProfessional(int $professionalId) {
        return Service::onlyTrashed()
            ->where('professional_id', $professionalId)
            ->get();
    }

    public function getDeletedServiceById(int $id) {
        return Service::onlyTrashed()->find($id);
    }

    public function restoreService(Service $service) {
        $service->restore();

        return $service->fresh();
    }
}
