<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;


class ServiceService {
    public function createServices(array $data, ?UploadedFile $coverImage = null, array $galleryImages = []) {
        return DB::transaction(function () use ($data, $coverImage, $galleryImages) {
            $service = Service::create($data);

            if ($coverImage) {
                $this->createServiceImage($service->id, $coverImage, true);
            }

            foreach ($galleryImages as $galleryImage) {
                if ($galleryImage instanceof UploadedFile) {
                    $this->createServiceImage($service->id, $galleryImage, false);
                }
            }

            return $service->load(['images', 'category']);
        });
    }

    public function getAllServicesForAdmin()
    {
        return Service::with(['category', 'professional.user', 'images'])
            ->withCount(['requests', 'reviews'])
            ->latest()
            ->get();
    }

    public function getPublishedServicesCount(): int
    {
        return Service::count();
    }

    public function getAllServices(int $perPage = 10, array $filters = []) {
        $query = Service::with(['category', 'professional.user', 'images']);

        if (!empty($filters['query'])) {
            $search = $filters['query'];

            $query->where(function ($serviceQuery) use ($search) {
                $serviceQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('professional.user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['category'])) {
            $category = $filters['category'];

            $query->whereHas('category', function ($categoryQuery) use ($category) {
                $categoryQuery->where('name', 'like', "%{$category}%");
            });
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'like', "%{$filters['city']}%");
        }

        $sort = $filters['sort'] ?? 'Newest';

        if ($sort === 'Top Rated') {
            $query->orderByDesc('rating');
        } elseif ($sort === 'Price: Low to High') {
            $query->orderBy('price_min');
        } elseif ($sort === 'Price: High to Low') {
            $query->orderByDesc('price_min');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getServiceById(int $id) {
        return Service::find($id);
    }

    public function getServiceDetailsById(int $id) {
        return Service::with([
            'category',
            'images',
            'professional.user',
            'professional.category',
            'reviews.client',
            'requests.client',
            'requests.review',
        ])->find($id);
    }

    public function getServiceCities() {
        return Service::query()
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
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

    public function getServicesByProfessional(int $professionalId) {
        return Service::with(['category', 'images', 'requests'])
            ->where('professional_id', $professionalId)
            ->latest()
            ->get();
    }

    public function getProfessionalServicesSummary(int $professionalId) {
        $services = Service::query()
            ->where('professional_id', $professionalId)
            ->get(['categorie_id', 'price_min', 'price_max', 'rating']);

        return [
            'published_services' => $services->count(),
            'priced_offers' => $services->filter(function ($service) {
                return $service->price_min || $service->price_max;
            })->count(),
            'categories' => $services->pluck('categorie_id')->filter()->unique()->count(),
            'average_rating' => round((float) $services->avg('rating'), 1),
        ];
    }

    protected function createServiceImage(int $serviceId, UploadedFile $image, bool $isPrimary): ServiceImage
    {
        $path = $image->store('services', 'public');

        return ServiceImage::create([
            'service_id' => $serviceId,
            'image_url' => $path,
            'is_primary' => $isPrimary,
        ]);
    }
}
