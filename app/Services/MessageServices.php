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

    public function createMessage(array $data)
    {
        return Messages::create($data);
    }
}
