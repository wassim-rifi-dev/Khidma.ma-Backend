<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\professional;
use App\Models\Service;

class ProfessionalServices {
    public function create(array $data) {
        return professional::create($data);
    }

    public function getProfessionalInfo(int $user_id) {
        return professional::where('user_id' , $user_id)->first();
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
}
