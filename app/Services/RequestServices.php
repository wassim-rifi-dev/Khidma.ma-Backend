<?php

namespace App\Services;

use App\Models\Request;

class RequestServices
{
    public function createRequest(array $data)
    {
        return Request::create($data);
    }

    public function getRequestById(int $id)
    {
        return Request::find($id);
    }

    public function updateRequestStatus(Request $request, string $status)
    {
        $request->update([
            'status' => $status,
        ]);

        return $request->fresh();
    }
}
