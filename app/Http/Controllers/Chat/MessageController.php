<?php

namespace App\Http\Controllers\Chat;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Controllers\Controller;
use App\Services\Chat\MessageService;
use App\Services\Professional\ProfessionalService;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request, int $chatId, MessageService $messageService, ProfessionalService $professionalService) {
        $chat = $messageService->getChatById($chatId);

        if (!$chat) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Chat not found'
            ], 404);
        }

        $user = $request->user();
        $professional = $professionalService->getProfessionalInfo((int) $user->id);

        $isClient = $chat->client_id === $user->id;
        $isProfessional = $professional && $chat->professional_id === $professional->id;

        if (!$isClient && !$isProfessional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = array_merge($request->validated(), [
            'sender_id' => $user->id,
            'chat_id' => $chat->id,
        ]);

        $message = $messageService->createMessage($data);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message,
            ],
            'message' => 'Message created successfully'
        ], 201);
    }
}
