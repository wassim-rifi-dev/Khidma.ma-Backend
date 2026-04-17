<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Services\MessageServices;
use App\Services\ProfessionalServices;

class MessagesController extends Controller
{
    public function store(StoreMessageRequest $request, int $chatId, MessageServices $messageServices, ProfessionalServices $professionalServices) {
        $chat = $messageServices->getChatById($chatId);

        if (!$chat) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Chat not found'
            ], 404);
        }

        $user = $request->user();
        $professional = $professionalServices->getProfessionalInfo((int) $user->id);

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

        $message = $messageServices->createMessage($data);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message,
            ],
            'message' => 'Message created successfully'
        ], 201);
    }
}
