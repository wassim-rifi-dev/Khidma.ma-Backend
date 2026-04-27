<?php

namespace App\Services\Review;

use App\Models\Professional;
use App\Models\Service;
use App\Models\Review;

class ReviewService
{
    public function createReview(array $data)
    {
        return Review::create($data);
    }

    public function getServiceReviews(int $serviceId)
    {
        if (!Service::whereKey($serviceId)->exists()) {
            return null;
        }

        return Review::with(['client', 'order'])
            ->whereHas('order', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->latest()
            ->get();
    }

    public function getServiceAverageRating(int $serviceId)
    {
        return (float) (Review::whereHas('order', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->avg('rating') ?? 0);
    }

    public function getProfessionalAverageRating(int $professionalId)
    {
        return (float) (Review::whereHas('order.service', function ($query) use ($professionalId) {
            $query->where('professional_id', $professionalId);
        })->avg('rating') ?? 0);
    }

    public function getServiceReviewsCount(int $serviceId)
    {
        return Review::whereHas('order', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->count();
    }

    public function getProfessionalReviewsCount(int $professionalId)
    {
        return Review::whereHas('order.service', function ($query) use ($professionalId) {
            $query->where('professional_id', $professionalId);
        })->count();
    }

    public function getClientReviewsCount(int $clientId)
    {
        return Review::where('client_id', $clientId)->count();
    }

    public function updateProfessionalRating(int $professionalId)
    {
        $professional = Professional::find($professionalId);

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
