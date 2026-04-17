<?php

namespace App\Services;

use App\Models\Request;

class RequestServices
{
    public function createRequest(array $data)
    {
        return Request::create($data);
    }
}
