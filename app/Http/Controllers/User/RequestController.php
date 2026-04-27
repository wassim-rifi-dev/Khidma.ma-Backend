<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\Request\RequestCreatedProfessionalMail;
use App\Http\Requests\Request\StoreRequestRequest;
use App\Http\Requests\Request\UpdateRequestStatusRequest;
use App\Services\Chat\MessageService;
use App\Services\Professional\ProfessionalService;
use App\Services\Request\RequestService;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    public function adminOpenRequestsCount(RequestService $requestService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'open_requests' => $requestService->getOpenRequestsCount(),
            ],
            'message' => 'Open requests count retrieved successfully'
        ], 200);
    }

    public function adminLatestRequests(RequestService $requestService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requestService->getLatestRequestsForAdmin(),
            ],
            'message' => 'Latest admin requests retrieved successfully'
        ], 200);
    }

    public function clientRequest(RequestService $requestService)
    {
        $requests = $requestService->getClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function clientRequestsCount(RequestService $requestService)
    {
        $count = $requestService->getClientRequestsCount((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Client requests count retrieved successfully'
        ], 200);
    }

    public function completedClientRequestsCount(RequestService $requestService)
    {
        $count = $requestService->getCompletedClientRequestsCount((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Completed client requests count retrieved successfully'
        ], 200);
    }

    public function lastThreeClientRequest(RequestService $requestService) {
        $requests = $requestService->getLastThreeClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function lastSixClientRequest(RequestService $requestService)
    {
        $requests = $requestService->getLastSixClientRequests((int) request()->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Client requests retrieved successfully'
        ], 200);
    }

    public function professionalRequest(RequestService $requestService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $requests = $requestService->getProfessionalRequests($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
            'message' => 'Professional requests retrieved successfully'
        ], 200);
    }

    public function professionalRequestDetails(int $id, RequestService $requestService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $requestDetails = $requestService->getProfessionalRequestById($id, $professional->id);

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

    public function store(StoreRequestRequest $request, int $serviceId, RequestService $requestService, MessageService $messageService)
    {
        $data = array_merge($request->validated(), [
            'client_id' => $request->user()->id,
            'service_id' => $serviceId,
        ]);

        $newRequest = $requestService->createRequest($data);
        $newRequest->load('service.professional.user', 'client');
        $service = $newRequest->service;
        $chat = null;

        if ($service) {
            $chat = $messageService->getChatByParticipants($request->user()->id, $service->professional_id);

            if (!$chat) {
                $chat = $messageService->createChat([
                    'client_id' => $request->user()->id,
                    'professional_id' => $service->professional_id,
                ]);
            }

            $messageService->createMessage([
                'sender_id' => $request->user()->id,
                'chat_id' => $chat->id,
                'message' => 'New service request',
                'message_type' => 'request',
                'media_url' => json_encode($this->buildRequestChatPayload($newRequest)),
            ]);

            $professionalEmail = $service->professional?->user?->email;

            if ($professionalEmail) {
                Mail::to($professionalEmail)->send(new RequestCreatedProfessionalMail($newRequest));
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $newRequest,
                'chat_id' => $chat?->id,
            ],
            'message' => 'Request created successfully'
        ], 201);
    }

    protected function buildRequestChatPayload($request): array
    {
        $service = $request->service;
        $professionalUser = $service?->professional?->user;

        return [
            'request_id' => $request->id,
            'service_id' => $service?->id,
            'service_title' => $service?->title,
            'professional_name' => $professionalUser?->name,
            'client_message' => $request->message,
            'preferred_date' => $request->preferred_date
                ? Carbon::parse($request->preferred_date)->format('Y-m-d')
                : null,
            'preferred_time' => $request->preferred_time,
            'address' => $request->address,
            'price' => $request->price !== null ? number_format((float) $request->price, 2, '.', '') : null,
            'currency' => 'MAD',
            'status' => $request->status,
        ];
    }

    public function cancel(int $id, RequestService $requestService)
    {
        $clientRequest = $requestService->getClientRequestById($id, (int) request()->user()->id);

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

        $canceledRequest = $requestService->cancelRequest($clientRequest);

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $canceledRequest,
            ],
            'message' => 'Request canceled successfully'
        ], 200);
    }

    public function updateStatus(UpdateRequestStatusRequest $request, int $id, RequestService $requestService, ProfessionalService $professionalService, MessageService $messageService) {
        $professional = $professionalService->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $clientRequest = $requestService->getRequestById($id);

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

        $newStatus = $request->validated()['status'];
        $updatedRequest = $requestService->updateRequestStatus($clientRequest, $newStatus);

        if (!$updatedRequest) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => sprintf(
                    'Cannot change request status from %s to %s',
                    $clientRequest->status,
                    $newStatus
                ),
            ], 422);
        }

        $updatedRequest->load('service.professional.user');

        $messageService->syncRequestPayload($updatedRequest);

        $chat = $messageService->getChatByParticipants((int) $updatedRequest->client_id, (int) $updatedRequest->service?->professional_id);

        if ($chat && in_array($newStatus, ['En_Cour', 'Refuser', 'Terminer'], true)) {
            $messageService->createMessage([
                'sender_id' => $request->user()->id,
                'chat_id' => $chat->id,
                'message' => match ($newStatus) {
                    'En_Cour' => 'Request accepted. We can continue the conversation here.',
                    'Terminer' => 'Request marked as completed. The client can now leave a review.',
                    default => 'Request declined. Feel free to discuss details or send a new request.',
                },
                'message_type' => 'text',
                'media_url' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $updatedRequest,
            ],
            'message' => 'Request status updated successfully'
        ], 200);
    }
}
