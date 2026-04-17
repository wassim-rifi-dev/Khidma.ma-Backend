<?php

namespace App\Services;

use App\Models\professional;
use App\Models\Service;
use App\Models\Reviews;

class ReviewServices
{
    public function createReview(array $data)
    {
        return Reviews::create($data);
    }

    public function getServiceAverageRating(int $serviceId)
    {
        return (float) (Reviews::whereHas('order', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->avg('rating') ?? 0);
    }

    public function getProfessionalAverageRating(int $professionalId)
    {
        return (float) (Reviews::whereHas('order.service', function ($query) use ($professionalId) {
            $query->where('professional_id', $professionalId);
        })->avg('rating') ?? 0);
    }

    public function getServiceReviewsCount(int $serviceId)
    {
        return Reviews::whereHas('order', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->count();
    }

    public function getProfessionalReviewsCount(int $professionalId)
    {
        return Reviews::whereHas('order.service', function ($query) use ($professionalId) {
            $query->where('professional_id', $professionalId);
        })->count();
    }

    public function updateProfessionalRating(int $professionalId)
    {
        $professional = professional::find($professionalId);

        if (!$professional) {
            return null;
        }

        $professional->update([
            'rating' => $this->getProfessionalAverageRating($professionalId),
        ]);

        return $professional->fresh();
    }

    public function updateServiceRating(int $serviceId)
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return null;
        }

        $service->update([
            'rating' => $this->getServiceAverageRating($serviceId),
        ]);

        return $service->fresh();
    }
}
