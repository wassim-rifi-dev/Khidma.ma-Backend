<?php

namespace App\Http\Controllers;

use App\Services\MessageServices;
use App\Services\ProfessionalServices;

class ChatController extends Controller
{
    public function index(int $chatId, MessageServices $messageServices, ProfessionalServices $professionalServices) {
        $chat = $messageServices->getChatById($chatId);

        if (!$chat) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Chat not found'
            ], 404);
        }

        $user = request()->user();
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

        $messages = $messageServices->getAllChatMessages($chatId);

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
            ],
            'message' => 'Chat messages retrieved successfully'
        ], 200);
    }
}
