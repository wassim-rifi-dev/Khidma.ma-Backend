<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\StoreRequestRequest;
use App\Services\RequestServices;

class RequestController extends Controller
{
    public function store(StoreRequestRequest $request, int $serviceId, RequestServices $requestServices)
    {
        $data = array_merge($request->validated(), [
            'client_id' => $request->user()->id,
            'service_id' => $serviceId,
        ]);

        $newRequest = $requestServices->createRequest($data);

        return response()->json([
            'success' => true,
            'data' => [
                'request' => $newRequest,
            ],
            'message' => 'Request created successfully'
        ], 201);
    }
}
