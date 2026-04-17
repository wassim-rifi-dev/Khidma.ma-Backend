<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\RequestServices;
use App\Services\ReviewServices;

class ReviewsController extends Controller
{
    public function index(ReviewServices $reviewServices , int $serviceId)
    {
        $serviceReviews = $reviewServices->getServiceReviews($serviceId);

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

    public function store(StoreReviewRequest $request, int $orderId, ReviewServices $reviewServices, RequestServices $requestServices)
    {
        $order = $requestServices->getRequestById($orderId);

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

        $review = $reviewServices->createReview($data);

        if ($order->service) {
            $reviewServices->updateServiceRating($order->service->id);
            $reviewServices->updateProfessionalRating($order->service->professional_id);
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
