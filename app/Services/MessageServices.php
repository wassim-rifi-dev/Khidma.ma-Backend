<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Messages;

class MessageServices
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
        return Messages::create($data);
    }
}
