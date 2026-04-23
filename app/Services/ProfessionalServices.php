<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\professional;
use App\Models\Reviews;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProfessionalServices {
    public function create(array $data) {
        return professional::create($data);
    }

    public function getAllProfessionals()
    {
        return professional::with(['user', 'category'])
            ->withCount(['services', 'requests'])
            ->latest()
            ->get();
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

    public function getProfessionalAnalytics(int $userId)
    {
        $professional = professional::with([
            'services.category',
            'services.requests.review',
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

        $services = $professional->services ?? collect();
        $requests = $services->flatMap(fn ($service) => $service->requests ?? collect())
            ->where('is_canceled', false)
            ->values();

        $reviews = $requests->map->review->filter()->values();

        $stats = $this->buildAnalyticsStats($professional, $requests, $reviews);
        $requestsOverTime = $this->buildRequestsOverTime($requests);
        $requestStatusItems = $this->buildRequestStatusItems($requests);
        $topServices = $this->buildTopServices($services);
        $ratingSummary = $this->buildRatingSummary($reviews);

        return [
            'stats' => $stats,
            'requests_over_time' => $requestsOverTime,
            'request_status_items' => $requestStatusItems,
            'request_status_total' => (string) $requests->count(),
            'top_services' => $topServices,
            'rating_summary' => $ratingSummary,
            'performance_tip' => [
                'title' => 'Improve your performance',
                'description' => 'Respond to requests within 15 minutes to increase your visibility. Complete your profile details to gain more trust.',
                'actionLabel' => 'Improve Profile',
            ],
        ];
    }

    protected function buildAnalyticsStats(professional $professional, Collection $requests, Collection $reviews): array
    {
        $responseRate = $requests->count() > 0
            ? round(($requests->whereIn('status', ['En_Cour', 'Terminer'])->count() / $requests->count()) * 100)
            : 0;

        return [
            [
                'label' => 'Total Requests',
                'value' => (string) $requests->count(),
                'trend' => '0%',
                'trendStyle' => 'bg-slate-100 text-slate-500',
                'iconKey' => 'requests',
                'iconStyle' => 'bg-sky-100 text-sky-700',
            ],
            [
                'label' => 'Completed Jobs',
                'value' => (string) $professional->completed_requests_count,
                'trend' => '0%',
                'trendStyle' => 'bg-slate-100 text-slate-500',
                'iconKey' => 'completed',
                'iconStyle' => 'bg-orange-100 text-orange-700',
            ],
            [
                'label' => 'Average Rating',
                'value' => number_format((float) ($professional->rating ?? $reviews->avg('rating') ?? 0), 1),
                'trend' => '0%',
                'trendStyle' => 'bg-slate-100 text-slate-500',
                'iconKey' => 'rating',
                'iconStyle' => 'bg-slate-200 text-slate-700',
            ],
            [
                'label' => 'Response Rate',
                'value' => $responseRate . '%',
                'trend' => '0%',
                'trendStyle' => 'bg-slate-100 text-slate-500',
                'iconKey' => 'response',
                'iconStyle' => 'bg-emerald-100 text-emerald-700',
            ],
        ];
    }

    protected function buildRequestsOverTime(Collection $requests): array
    {
        $weeks = collect(range(3, 0))->reverse()->map(function ($offset) {
            $start = Carbon::now()->startOfWeek()->subWeeks($offset);

            return [
                'label' => 'Week ' . (4 - $offset),
                'start' => $start,
                'end' => $start->copy()->endOfWeek(),
            ];
        });

        return [
            'labels' => $weeks->pluck('label')->values()->all(),
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $weeks->map(fn ($week) => $requests->filter(
                        fn ($request) => $request->created_at && Carbon::parse($request->created_at)->between($week['start'], $week['end'])
                    )->count())->values()->all(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.08)',
                    'pointBackgroundColor' => '#f97316',
                ],
                [
                    'label' => 'Completed',
                    'data' => $weeks->map(fn ($week) => $requests->filter(
                        fn ($request) => $request->status === 'Terminer'
                            && $request->created_at
                            && Carbon::parse($request->created_at)->between($week['start'], $week['end'])
                    )->count())->values()->all(),
                    'borderColor' => '#bfdbfe',
                    'backgroundColor' => 'rgba(191, 219, 254, 0.16)',
                    'pointBackgroundColor' => '#bfdbfe',
                ],
            ],
        ];
    }

    protected function buildRequestStatusItems(Collection $requests): array
    {
        $total = max($requests->count(), 1);

        return [
            [
                'label' => 'New',
                'value' => round(($requests->where('status', 'Nouveau')->count() / $total) * 100),
                'color' => '#0073ad',
            ],
            [
                'label' => 'Progress',
                'value' => round(($requests->where('status', 'En_Cour')->count() / $total) * 100),
                'color' => '#f97316',
            ],
            [
                'label' => 'Done',
                'value' => round(($requests->where('status', 'Terminer')->count() / $total) * 100),
                'color' => '#2ea84f',
            ],
        ];
    }

    protected function buildTopServices(Collection $services): array
    {
        $topServices = $services
            ->map(function ($service) {
                $requestsCount = collect($service->requests ?? [])->where('is_canceled', false)->count();

                return [
                    'name' => $service->title,
                    'requests' => $requestsCount,
                ];
            })
            ->sortByDesc('requests')
            ->take(3)
            ->values();

        $maxRequests = max((int) ($topServices->max('requests') ?? 0), 1);
        $colors = ['bg-orange-500', 'bg-sky-800', 'bg-slate-600'];

        return $topServices->map(function ($service, $index) use ($maxRequests, $colors) {
            return [
                'name' => $service['name'],
                'requests' => $service['requests'],
                'percentage' => round(($service['requests'] / $maxRequests) * 100),
                'color' => $colors[$index] ?? 'bg-slate-500',
            ];
        })->all();
    }

    protected function buildRatingSummary(Collection $reviews): array
    {
        $ratings = collect([5, 4, 3, 2, 1])->map(function ($score) use ($reviews) {
            $count = $reviews->filter(fn ($review) => (int) round($review->rating) === $score)->count();
            $percentage = $reviews->count() > 0 ? round(($count / $reviews->count()) * 100) : 0;

            return [
                'score' => $score,
                'percentage' => $percentage,
            ];
        })->all();

        return [
            'average' => number_format((float) ($reviews->avg('rating') ?? 0), 1),
            'reviewsLabel' => 'Based on ' . $reviews->count() . ' reviews',
            'ratings' => $ratings,
        ];
    }
}
