<?php

namespace App\Http\Controllers;

use App\Services\MessageServices;
use App\Http\Requests\Request\StoreRequestRequest;
use App\Http\Requests\Request\UpdateRequestStatusRequest;
use App\Services\ProfessionalServices;
use App\Services\RequestServices;

class RequestController extends Controller
{
    public function clientRequest(RequestServices $requestServices)
    {
        $requests = $requestServices->getClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function clientRequestsCount(RequestServices $requestServices)
    {
        $count = $requestServices->getClientRequestsCount((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Client requests count retrieved successfully'
        ], 200);
    }

    public function completedClientRequestsCount(RequestServices $requestServices)
    {
        $count = $requestServices->getCompletedClientRequestsCount((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Completed client requests count retrieved successfully'
        ], 200);
    }

    public function lastThreeClientRequest(RequestServices $requestServices) {
        $requests = $requestServices->getLastThreeClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function lastSixClientRequest(RequestServices $requestServices)
    {
        $requests = $requestServices->getLastSixClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function professionalRequest(RequestServices $requestServices, ProfessionalServices $professionalServices)
    {
        $professional = $professionalServices->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $requests = $requestServices->getProfessionalRequests($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Professional requests retrieved successfully'
        ], 200);
    }

    public function professionalRequestDetails(int $id, RequestServices $requestServices, ProfessionalServices $professionalServices)
    {
        $professional = $professionalServices->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $requestDetails = $requestServices->getProfessionalRequestById($id, $professional->id);

        if (!$requestDetails) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Request not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $requestDetails,
            ],
            'message' => 'Professional request retrieved successfully'
        ], 200);
    }

    public function store(StoreRequestRequest $request, int $serviceId, RequestServices $requestServices, MessageServices $messageServices)
    {
        $data = array_merge($request->validated(), [
            'client_id' => $request->user()->id,
            'service_id' => $serviceId,
        ]);

        $newRequest = $requestServices->createRequest($data);
        $service = $newRequest->service;

        if ($service) {
            $chat = $messageServices->getChatByParticipants($request->user()->id, $service->professional_id);

            if (!$chat) {
                $chat = $messageServices->createChat([
                    'client_id' => $request->user()->id,
                    'professional_id' => $service->professional_id,
                ]);
            }

            $messageServices->createMessage([
                'sender_id' => $request->user()->id,
                'chat_id' => $chat->id,
                'message' => $newRequest->message,
                'message_type' => 'request',
                'media_url' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $newRequest,
            ],
            'message' => 'Request created successfully'
        ], 201);
    }

    public function cancel(int $id, RequestServices $requestServices)
    {
        $clientRequest = $requestServices->getClientRequestById($id, (int) request()->user()->id);

        if (!$clientRequest) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Request not found'
            ], 404);
        }

        if ($clientRequest->is_canceled) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Request is already canceled'
            ], 409);
        }

        if ($clientRequest->status !== 'Nouveau') {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Only new requests can be canceled'
            ], 422);
        }

        $canceledRequest = $requestServices->cancelRequest($clientRequest);

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $canceledRequest,
            ],
            'message' => 'Request canceled successfully'
        ], 200);
    }

    public function updateStatus(UpdateRequestStatusRequest $request, int $id, RequestServices $requestServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $clientRequest = $requestServices->getRequestById($id);

        if (!$clientRequest) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Request not found'
            ], 404);
        }

        if (!$clientRequest->service || $clientRequest->service->professional_id !== $professional->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        $updatedRequest = $requestServices->updateRequestStatus($clientRequest, $request->validated()['status']);

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $updatedRequest,
            ],
            'message' => 'Request status updated successfully'
        ], 200);
    }
}
