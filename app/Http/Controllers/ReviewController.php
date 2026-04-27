<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\Request\RequestService;
use App\Services\Review\ReviewService;

class ReviewController extends Controller
{
    public function index(ReviewService $reviewService , int $serviceId)
    {
        $serviceReviews = $reviewService->getServiceReviews($serviceId);

        if (!$serviceReviews) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'serviceReviews' => $serviceReviews,
            ],
            'message' => 'Service reviews retrieved successfully'
        ], 200);
    }

    public function clientReviewsCount(ReviewService $reviewService)
    {
        $count = $reviewService->getClientReviewsCount((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Client reviews count retrieved successfully'
        ], 200);
    }

    public function store(StoreReviewRequest $request, int $orderId, ReviewService $reviewService, RequestService $requestService)
    {
        $order = $requestService->getRequestById($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($order->status !== 'Terminer') {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Order en cours il ne termine pas'
            ], 404);
        }

        if ($order->review) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Review already exists for this order'
            ], 409);
        }

        $data = array_merge($request->validated(), [
            'order_id' => $orderId,
            'client_id' => $request->user()->id,
        ]);

        $review = $reviewService->createReview($data);

        if ($order->service) {
            $reviewService->updateServiceRating($order->service->id);
            $reviewService->updateProfessionalRating($order->service->professional_id);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'review' => $review,
            ],
            'message' => 'Review created successfully'
        ], 201);
    }
}
