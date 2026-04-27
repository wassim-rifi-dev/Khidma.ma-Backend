<?php

namespace App\Services\Chat;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Request as ServiceRequest;
use Illuminate\Support\Collection;

class MessageService
{
    public function getChatById(int $id)
    {
        return Chat::find($id);
    }

    public function getChatByParticipants(int $clientId, int $professionalId)
    {
        return Chat::where('client_id', $clientId)
            ->where('professional_id', $professionalId)
            ->first();
    }

    public function createChat(array $data)
    {
        return Chat::create($data);
    }

    public function createMessage(array $data)
    {
        $message = Message::create($data);

        $message->chat()->touch();

        return $message->load('sender');
    }

    public function getAllChatMessages(int $chatId)
    {
        return Message::with('sender')
            ->where('chat_id', $chatId)
            ->oldest()
            ->get();
    }

    public function getChatsForClient(int $clientId): Collection
    {
        return Chat::with(['client', 'professional.user', 'latestMessage.sender'])
            ->where('client_id', $clientId)
            ->latest('updated_at')
            ->get();
    }

    public function getChatsForProfessional(int $professionalId): Collection
    {
        return Chat::with(['client', 'professional.user', 'latestMessage.sender'])
            ->where('professional_id', $professionalId)
            ->latest('updated_at')
            ->get();
    }

    public function getChatsForUser(int $userId, ?int $professionalId = null): Collection
    {
        $clientChats = $this->getChatsForClient($userId);

        if (!$professionalId) {
            return $clientChats;
        }

        return $clientChats
            ->merge($this->getChatsForProfessional($professionalId))
            ->unique('id')
            ->sortByDesc(function ($chat) {
                return optional($chat->updated_at)->timestamp ?? 0;
            })
            ->values();
    }

    public function syncRequestPayload(ServiceRequest $request): void
    {
        $requestId = (int) $request->id;

        Message::where('message_type', 'request')
            ->where('media_url', 'like', '%"request_id":'.$requestId.'%')
            ->get()
            ->each(function (Message $message) use ($request) {
                $payload = json_decode($message->media_url ?? '', true);

                if (!is_array($payload)) {
                    return;
                }

                $payload['status'] = $request->status;
                $message->update([
                    'media_url' => json_encode($payload),
                ]);
            });
    }
}
