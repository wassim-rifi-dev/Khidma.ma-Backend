<?php

namespace App\Services;

use App\Models\Request;

class RequestServices
{
    public function createRequest(array $data)
    {
        return Request::create($data);
    }

    public function getClientRequests(int $clientId)
    {
        return Request::with('service.professional.user')
            ->where('client_id', $clientId)
            ->where('is_Cancled', false)
            ->get();
    }

    public function getClientRequestsCount(int $clientId) {
        return Request::with('service.professional.user')
            ->where('client_id', $clientId)
            ->count();
    }

    public function getLastThreeClientRequests(int $clientId)
    {
        return Request::with('service.professional.user')
            ->where('client_id', $clientId)
            ->where('is_Cancled', false)
            ->latest()
            ->limit(3)
            ->get();
    }

    public function getProfessionalRequests(int $professionalId)
    {
        return Request::with(['client', 'service'])
            ->whereHas('service', function ($query) use ($professionalId) {
                $query->where('professional_id', $professionalId);
            })
            ->get();
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
