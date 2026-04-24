<?php

namespace App\Services;

use App\Models\professional;
use App\Models\Request;
use App\Models\Reviews;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminAnalyticsService
{
    public function getAnalyticsOverview(): array
    {
        $users = User::query()->get(['id', 'role', 'is_active', 'created_at']);
        $professionals = professional::query()->get(['id', 'is_verified']);
        $services = Service::with('category')->get(['id', 'categorie_id', 'created_at']);
        $requests = Request::with('service.category')
            ->where('is_canceled', false)
            ->get(['id', 'service_id', 'status', 'created_at']);
        $reviews = Reviews::query()->get(['id', 'rating']);

        return [
            'summary_cards' => $this->buildSummaryCards($users, $services, $requests),
            'monthly_activity' => $this->buildMonthlyActivity($requests),
            'top_categories' => $this->buildTopCategories($requests),
            'performance_summary' => $this->buildPerformanceSummary($users, $professionals, $requests, $reviews),
            'quick_insights' => $this->buildQuickInsights($users, $professionals, $requests, $reviews),
        ];
    }

    protected function buildSummaryCards(Collection $users, Collection $services, Collection $requests): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            [
                'label' => 'Total users',
                'value' => $users->count(),
            ],
            [
                'label' => 'New users this month',
                'value' => $users->filter(
                    fn ($user) => $user->created_at && Carbon::parse($user->created_at)->greaterThanOrEqualTo($startOfMonth)
                )->count(),
            ],
            [
                'label' => 'Completed requests',
                'value' => $requests->where('status', 'Terminer')->count(),
            ],
            [
                'label' => 'Active services',
                'value' => $services->count(),
            ],
        ];
    }

    protected function buildMonthlyActivity(Collection $requests): array
    {
        $months = collect(range(5, 0))->map(function ($offset) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);

            return [
                'label' => $month->format('M'),
                'start' => $month,
                'end' => $month->copy()->endOfMonth(),
            ];
        })->values();

        $requestsData = $months->map(fn ($month) => $requests->filter(
            fn ($request) => $request->created_at && Carbon::parse($request->created_at)->between($month['start'], $month['end'])
        )->count())->values();

        $completedData = $months->map(fn ($month) => $requests->filter(
            fn ($request) => $request->status === 'Terminer'
                && $request->created_at
                && Carbon::parse($request->created_at)->between($month['start'], $month['end'])
        )->count())->values();

        $currentMonthTotal = (int) $requestsData->last();
        $previousMonthTotal = (int) ($requestsData->slice(-2, 1)->first() ?? 0);

        return [
            'labels' => $months->pluck('label')->all(),
            'requests' => $requestsData->all(),
            'completed' => $completedData->all(),
            'trend' => $this->formatTrend($currentMonthTotal, $previousMonthTotal),
        ];
    }

    protected function buildTopCategories(Collection $requests): array
    {
        $totalRequests = max($requests->count(), 1);

        return $requests
            ->groupBy(fn ($request) => $request->service?->category?->name ?? 'Uncategorized')
            ->map(function ($group, $name) use ($totalRequests) {
                $count = $group->count();

                return [
                    'name' => $name,
                    'value' => round(($count / $totalRequests) * 100) . '%',
                    'count' => $count,
                ];
            })
            ->sortByDesc('count')
            ->take(4)
            ->values()
            ->all();
    }

    protected function buildPerformanceSummary(
        Collection $users,
        Collection $professionals,
        Collection $requests,
        Collection $reviews
    ): array {
        $totalRequests = $requests->count();
        $completedRequests = $requests->where('status', 'Terminer')->count();
        $verifiedProfessionals = $professionals->where('is_verified', true)->count();
        $activeAccounts = $users->where('is_active', true)->count();

        return [
            [
                'label' => 'Completion rate',
                'value' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) . '%' : '0%',
            ],
            [
                'label' => 'Average review score',
                'value' => number_format((float) ($reviews->avg('rating') ?? 0), 1),
            ],
            [
                'label' => 'Verified professionals',
                'value' => $verifiedProfessionals,
            ],
            [
                'label' => 'Active accounts',
                'value' => $activeAccounts,
            ],
        ];
    }

    protected function buildQuickInsights(
        Collection $users,
        Collection $professionals,
        Collection $requests,
        Collection $reviews
    ): array {
        $topCategory = collect($this->buildTopCategories($requests))->first();
        $newProfessionalsThisMonth = $users
            ->where('role', 'professional')
            ->filter(fn ($user) => $user->created_at && Carbon::parse($user->created_at)->greaterThanOrEqualTo(Carbon::now()->startOfMonth()))
            ->count();
        $newClientsThisMonth = $users
            ->where('role', 'client')
            ->filter(fn ($user) => $user->created_at && Carbon::parse($user->created_at)->greaterThanOrEqualTo(Carbon::now()->startOfMonth()))
            ->count();
        $verifiedShare = $professionals->count() > 0
            ? round(($professionals->where('is_verified', true)->count() / $professionals->count()) * 100)
            : 0;

        return array_values(array_filter([
            $topCategory
                ? sprintf(
                    '%s leads demand with %d requests (%s of platform activity).',
                    $topCategory['name'],
                    $topCategory['count'],
                    $topCategory['value']
                )
                : null,
            sprintf(
                'New professional signups this month: %d, compared with %d new client accounts.',
                $newProfessionalsThisMonth,
                $newClientsThisMonth
            ),
            sprintf(
                'Average review score is %s and %d%% of professionals are verified.',
                number_format((float) ($reviews->avg('rating') ?? 0), 1),
                $verifiedShare
            ),
        ]));
    }

    protected function formatTrend(int $currentValue, int $previousValue): string
    {
        if ($previousValue === 0) {
            return $currentValue > 0 ? '+100%' : '0%';
        }

        $change = (($currentValue - $previousValue) / $previousValue) * 100;
        $rounded = round($change, 1);
        $prefix = $rounded > 0 ? '+' : '';

        return $prefix . $rounded . '%';
    }
}
