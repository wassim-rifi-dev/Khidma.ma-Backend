<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageServices;
use App\Services\ProfessionalServices;

class ChatController extends Controller
{
    public function conversations(MessageServices $messageServices, ProfessionalServices $professionalServices) {
        $user = request()->user();
        $professional = $professionalServices->getProfessionalInfo((int) $user->id);
        $chats = $messageServices->getChatsForUser((int) $user->id, $professional?->id);

        $conversations = $chats->map(function ($chat) use ($user, $professional) {
            $participant = $professional && $chat->professional_id === $professional->id
                ? $chat->client
                : $chat->professional?->user;
            $participantRole = $participant?->role;
            $participantLocation = $participantRole === 'professional'
                ? $chat->professional?->city
                : null;
            $participantProfilePath = $participantRole === 'professional' && $chat->professional?->id
                ? '/professional/' . $chat->professional->id
                : null;

            return [
                'id' => $chat->id,
                'participant' => [
                    'id' => $participant?->id,
                    'name' => $participant?->name,
                    'photo' => $participant?->photo,
                    'role' => $participantRole,
                    'location' => $participantLocation,
                    'profile_path' => $participantProfilePath,
                ],
                'last_message' => $chat->latestMessage?->message,
                'last_message_id' => $chat->latestMessage?->id,
                'last_message_type' => $chat->latestMessage?->message_type,
                'last_message_media_url' => $chat->latestMessage?->media_url,
                'last_message_sender_id' => $chat->latestMessage?->sender_id,
                'last_message_at' => optional($chat->latestMessage?->created_at)->toISOString(),
                'updated_at' => optional($chat->updated_at)->toISOString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'conversations' => $conversations,
            ],
            'message' => 'Chat conversations retrieved successfully'
        ], 200);
    }

    public function stream(Request $request, MessageServices $messageServices, ProfessionalServices $professionalServices)
    {
        $user = $request->user();
        $professional = $professionalServices->getProfessionalInfo((int) $user->id);
        $cursor = max((int) $request->query('cursor', 0), 0);

        return response()->stream(function () use ($messageServices, $user, $professional, $cursor) {
            $lastCursor = $cursor;
            $startedAt = time();

            while (!connection_aborted() && (time() - $startedAt) < 30) {
                $messages = $messageServices->getMessagesForUserSince((int) $user->id, $professional?->id, $lastCursor);

                foreach ($messages as $message) {
                    $lastCursor = max($lastCursor, (int) $message->id);

                    echo "event: message.created\n";
                    echo 'data: ' . json_encode([
                        'cursor' => $lastCursor,
                        'message' => $message,
                    ]) . "\n\n";

                    @ob_flush();
                    flush();
                }

                if ((time() - $startedAt) % 10 === 0) {
                    echo "event: ping\n";
                    echo 'data: ' . json_encode([
                        'cursor' => $lastCursor,
                    ]) . "\n\n";

                    @ob_flush();
                    flush();
                }

                sleep(2);
            }
        }, 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function direct(int $professionalId, Request $request, MessageServices $messageServices, ProfessionalServices $professionalServices)
    {
        $user = $request->user();
        $targetProfessional = $professionalServices->getProfessionalById($professionalId);

        if (!$targetProfessional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional not found',
            ], 404);
        }

        if ((int) $targetProfessional->user_id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'You cannot create a chat with yourself',
            ], 422);
        }

        $chat = $messageServices->getChatByParticipants((int) $user->id, (int) $targetProfessional->id);
        $created = false;

        if (!$chat) {
            $chat = $messageServices->createChat([
                'client_id' => (int) $user->id,
                'professional_id' => (int) $targetProfessional->id,
            ]);
            $created = true;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'chat_id' => $chat->id,
                'created' => $created,
            ],
            'message' => $created ? 'Chat created successfully' : 'Chat retrieved successfully',
        ], $created ? 201 : 200);
    }

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
