<?php

namespace App\Services\Admin;

use App\Models\Professional;
use App\Models\Request;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function getAnalyticsOverview()
    {
        $users = User::query()->get(['id', 'role', 'is_active', 'created_at']);

        $professionals = Professional::query()->get(['id', 'is_verified']);

        $services = Service::with('category')->get(['id', 'categorie_id', 'created_at']);

        $requests = Request::with('service.category')
            ->where('is_canceled', false)
            ->get(['id', 'service_id', 'status', 'created_at']);

        $reviews = Review::query()->get(['id', 'rating']);

        return [
            'summary_cards' => $this->buildSummaryCards($users, $services, $requests),
            'monthly_activity' => $this->buildMonthlyActivity($requests),
            'top_categories' => $this->buildTopCategories($requests),
            'performance_summary' => $this->buildPerformanceSummary($users, $professionals, $requests, $reviews),
            'quick_insights' => $this->buildQuickInsights($users, $professionals, $requests, $reviews),
        ];
    }

    protected function buildSummaryCards(Collection $users, Collection $services, Collection $requests)
    {
        $startOfMonth = now()->startOfMonth();

        return [
            [
                'label' => 'Total users',
                'value' => $users->count(),
            ],
            [
                'label' => 'New users this month',
                'value' => $users->where('created_at' , '>=' , $startOfMonth)->count(),
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

    protected function buildMonthlyActivity(Collection $requests)
    {
        $months = collect(range(0, 5))->map(function ($i) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return [
                'label' => $start->format('M'),
                'start' => $start,
                'end' => $end,
            ];
        })->reverse()->values();

        $requestsData = $months->map(function ($month) use ($requests) {
            return $requests->whereBetween('created_at', [$month['start'], $month['end']])->count();
        });

        $completedData = $months->map(function ($month) use ($requests) {
            return $requests
                ->where('status', 'Terminer')
                ->whereBetween('created_at', [$month['start'], $month['end']])
                ->count();
        });

        $current = $requestsData->last();
        $previous = $requestsData->slice(-2, 1)->first() ?? 0;

        return [
            'labels' => $months->pluck('label'),
            'requests' => $requestsData,
            'completed' => $completedData,
            'trend' => $this->formatTrend($current, $previous),
        ];
    }

    protected function buildTopCategories(Collection $requests)
    {
        $totalRequests = max($requests->count(), 1);

        return $requests
            ->groupBy(function ($request) {
                return $request->service?->category?->name ?? 'Uncategorized';
            })
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

    protected function buildPerformanceSummary(Collection $users, Collection $professionals, Collection $requests, Collection $reviews) {
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

    protected function buildQuickInsights(Collection $users, Collection $professionals, Collection $requests, Collection $reviews) {
        $startOfMonth = Carbon::now()->startOfMonth();
        $topCategory = collect($this->buildTopCategories($requests))->first();
        $newUsersThisMonth = $users->filter(function ($user) use ($startOfMonth) {
            return $user->created_at && Carbon::parse($user->created_at)->greaterThanOrEqualTo($startOfMonth);
        });

        $newProfessionalsThisMonth = $newUsersThisMonth->where('role', 'professional')->count();
        $newClientsThisMonth = $newUsersThisMonth->where('role', 'client')->count();

        $verifiedShare = $professionals->count() > 0
            ? round(($professionals->where('is_verified', true)->count() / $professionals->count()) * 100)
            : 0;

        return array_values(array_filter([
            $topCategory ? sprintf(
                '%s leads demand with %d requests (%s of platform activity).',
                $topCategory['name'],
                $topCategory['count'],
                $topCategory['value']
            ) : null,
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

    protected function formatTrend(int $currentValue, int $previousValue)
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
