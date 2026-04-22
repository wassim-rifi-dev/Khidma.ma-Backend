<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\professional;
use App\Models\Reviews;
use App\Models\Service;

class ProfessionalServices {
    public function create(array $data) {
        return professional::create($data);
    }

    public function getProfessionalInfo(int $user_id) {
        return professional::where('user_id' , $user_id)->first();
    }

    public function getProfessionalDashboardProfile(int $userId)
    {
        $professional = professional::with([
            'user',
            'category',
            'services.category',
            'services.requests',
            'services.reviews.client',
        ])
            ->withCount([
                'services',
                'requests',
                'requests as completed_requests_count' => function ($query) {
                    $query->where('status', 'Terminer')->where('is_canceled', false);
                },
            ])
            ->where('user_id', $userId)
            ->first();

        if (!$professional) {
            return null;
        }

        $reviewsCount = Reviews::whereHas('order.service', function ($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->count();

        $recentReviews = Reviews::with(['client', 'order.service'])
            ->whereHas('order.service', function ($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->latest()
            ->limit(5)
            ->get();

        $professional->setAttribute('reviews_count', $reviewsCount);
        $professional->setAttribute('recent_reviews', $recentReviews);

        return $professional;
    }

    public function getCategoryByName(string $name) {
        return Categories::where('name', $name)->first();
    }

    public function getAllProfessionalServices(int $professionalId) {
        $professional = professional::find($professionalId);

        if (!$professional) {
            return null;
        }

        return Service::with(['professional', 'categorie', 'requests'])
            ->where('professional_id', $professionalId)
            ->get();
    }

    public function getTopProfessionals(int $limit = 2)
    {
        return professional::with(['user', 'category'])
            ->withCount(['services', 'requests'])
            ->orderByDesc('rating')
            ->limit($limit)
            ->get();
    }

    public function getProfessionalProfileById(int $professionalId)
    {
        return professional::with([
            'user',
            'category',
            'services.category',
            'services.images',
            'services.reviews.client',
        ])
            ->withCount(['services', 'requests'])
            ->find($professionalId);
    }
}
